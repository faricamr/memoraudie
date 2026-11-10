<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonErr('Method Not Allowed', 405);
}

$slug = trim($_POST['slug'] ?? '');
$guest_name = trim($_POST['guest_name'] ?? '');
$stickers = $_POST['stickers'] ?? '[]';

if ($slug === '') {
    jsonErr('slug is required', 400);
}
if ($guest_name === '' || mb_strlen($guest_name) > 80) {
    jsonErr('guest_name is required and must be max 80 characters', 400);
}

if (!isset($_FILES['audio']) || $_FILES['audio']['error'] !== UPLOAD_ERR_OK) {
    jsonErr('audio file is required or upload failed', 400);
}
if (!isset($_FILES['cover']) || $_FILES['cover']['error'] !== UPLOAD_ERR_OK) {
    jsonErr('cover image is required or upload failed', 400);
}

if ($_FILES['audio']['size'] > MAX_AUDIO_BYTES) {
    jsonErr('Audio file exceeds maximum size', 400);
}
if ($_FILES['cover']['size'] > MAX_COVER_BYTES) {
    jsonErr('Cover file exceeds maximum size', 400);
}

$audioMime = mime_content_type($_FILES['audio']['tmp_name']);
if (!in_array($audioMime, ['audio/webm', 'video/webm'], true)) {
    jsonErr('Audio file must be WebM', 400);
}

$coverMime = mime_content_type($_FILES['cover']['tmp_name']);
if ($coverMime !== 'image/jpeg') {
    jsonErr('Cover image must be JPEG', 400);
}

try {
    $db = get_db();

    $stmt = $db->prepare("SELECT * FROM moments WHERE slug = ?");
    $stmt->execute([$slug]);
    $moment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$moment) {
        jsonErr('Moment not found', 404);
    }

    $state = computeState($moment['start_time'], $moment['end_time']);
    if ($state !== 'active') {
        jsonErr('Moment is no longer active', 403);
    }

    if (!is_dir(TEMP_BASE)) {
        if (!mkdir(TEMP_BASE, 0755, true)) {
            jsonErr('Failed to create temporary directory', 500);
        }
    }

    $uniqueName = bin2hex(random_bytes(8));
    $tempAudio = TEMP_BASE . '/' . $uniqueName . '.webm';
    $tempCover = TEMP_BASE . '/' . $uniqueName . '.jpg';

    if (!move_uploaded_file($_FILES['audio']['tmp_name'], $tempAudio)) {
        jsonErr('Failed to save temporary audio', 500);
    }
    if (!move_uploaded_file($_FILES['cover']['tmp_name'], $tempCover)) {
        @unlink($tempAudio);
        jsonErr('Failed to save temporary cover', 500);
    }

    $audioDir = UPLOAD_BASE . '/' . $slug . '/audio';
    $coverDir = UPLOAD_BASE . '/' . $slug . '/covers';

    if (!is_dir($audioDir)) {
        @mkdir($audioDir, 0755, true);
    }
    if (!is_dir($coverDir)) {
        @mkdir($coverDir, 0755, true);
    }

    $finalAudio = $audioDir . '/' . $uniqueName . '.mp3';
    $finalCover = $coverDir . '/' . $uniqueName . '.jpg';

    // Convert WebM to MP3 and embed cover/metadata
    $dateStr = date('Y-m-d');
    $ffmpegCmd = escapeshellarg(FFMPEG_BIN) . " -i " . escapeshellarg($tempAudio);
    
    if (file_exists($tempCover)) {
        $ffmpegCmd .= " -i " . escapeshellarg($tempCover) . " -map 0:a -map 1 -c:v copy";
    } else {
        $ffmpegCmd .= " -map 0:a";
    }

    $ffmpegCmd .= " -ar 44100 -ac 2 -b:a 128k -id3v2_version 3"
        . " -metadata title=" . escapeshellarg($guest_name)
        . " -metadata artist=" . escapeshellarg($guest_name)
        . " -metadata album=" . escapeshellarg($moment['title'])
        . " -metadata date=" . escapeshellarg($dateStr)
        . " -metadata genre=\"Audio Memory\"";

    if (file_exists($tempCover)) {
        $ffmpegCmd .= " -metadata:s:v title=\"Album cover\" -metadata:s:v comment=\"Cover (front)\"";
    }

    $ffmpegCmd .= " -y " . escapeshellarg($finalAudio) . " 2>&1";
    
    exec($ffmpegCmd, $output, $returnVar);

    if ($returnVar !== 0 || !file_exists($finalAudio)) {
        @unlink($tempAudio);
        @unlink($tempCover);
        if (file_exists($finalAudio)) @unlink($finalAudio);
        jsonErr('Failed to convert audio', 500);
    }

    // Extract duration using ffprobe
    $ffprobeBin = str_replace('ffmpeg.exe', 'ffprobe.exe', FFMPEG_BIN);
    $duration = null;
    if (file_exists($ffprobeBin)) {
        $ffprobeCmd = escapeshellarg($ffprobeBin) . " -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 " . escapeshellarg($finalAudio);
        $durationOutput = exec($ffprobeCmd, $probeOut, $probeRet);
        if ($probeRet === 0 && is_numeric($durationOutput)) {
            $duration = (int) round((float) $durationOutput);
        }
    }

    // Move cover
    if (!rename($tempCover, $finalCover)) {
        @unlink($tempAudio);
        @unlink($tempCover);
        @unlink($finalAudio);
        jsonErr('Failed to save cover image', 500);
    }

    // Insert to database
    $audioFilename = $uniqueName . '.mp3';
    $coverFilename = $uniqueName . '.jpg';

    $stmtInsert = $db->prepare("
        INSERT INTO memories (moment_id, guest_name, audio_filename, audio_duration, cover_filename, stickers)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    $stmtInsert->execute([
        $moment['id'],
        $guest_name,
        $audioFilename,
        $duration,
        $coverFilename,
        $stickers
    ]);
    
    $memoryId = $db->lastInsertId();

    // Cleanup temporary files
    if (file_exists($tempAudio)) @unlink($tempAudio);
    if (file_exists($tempCover)) @unlink($tempCover);

    jsonOk([
        'memory_id' => (int) $memoryId,
        'guest_name' => $guest_name,
        'audio_filename' => $audioFilename,
        'audio_duration' => $duration,
        'cover_filename' => $coverFilename
    ]);

} catch (PDOException $e) {
    if (isset($tempAudio) && file_exists($tempAudio)) @unlink($tempAudio);
    if (isset($tempCover) && file_exists($tempCover)) @unlink($tempCover);
    if (isset($finalAudio) && file_exists($finalAudio)) @unlink($finalAudio);
    if (isset($finalCover) && file_exists($finalCover)) @unlink($finalCover);
    
    jsonErr('Database error', 500);
}
