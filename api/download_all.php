<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

set_time_limit(300);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    die("Method not allowed.");
}

$slug = trim($_GET['slug'] ?? '');
$hostKey = trim($_GET['host_key'] ?? '');

if (!$slug || !$hostKey) {
    die("Invalid request parameters.");
}

try {
    $db = get_db();

    // Verify Host Key
    $stmtHost = $db->prepare("SELECT id, title, host_key FROM moments WHERE slug = ?");
    $stmtHost->execute([$slug]);
    $moment = $stmtHost->fetch(PDO::FETCH_ASSOC);

    if (!$moment || $moment['host_key'] !== $hostKey) {
        die("Unauthorized access.");
    }

    $momentTitle = $moment['title'] ?: 'Memoraudie';

    // Fetch all memories
    $stmtMemories = $db->prepare("
        SELECT id, guest_name, audio_filename, created_at
        FROM memories 
        WHERE moment_id = ?
        ORDER BY created_at DESC
    ");
    $stmtMemories->execute([$moment['id']]);
    $memories = $stmtMemories->fetchAll(PDO::FETCH_ASSOC);

    if (empty($memories)) {
        die("No memories to download.");
    }

    if (!is_dir(TEMP_BASE)) {
        @mkdir(TEMP_BASE, 0755, true);
    }

    $zipId = bin2hex(random_bytes(8));
    $zipFilename = TEMP_BASE . '/moment_' . $zipId . '_memories.zip';
    
    $zip = new ZipArchive();
    if ($zip->open($zipFilename, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        die("Failed to create ZIP archive.");
    }

    $filenameCounts = [];

    foreach ($memories as $memory) {
        $audioPath = UPLOAD_BASE . '/' . $slug . '/audio/' . $memory['audio_filename'];

        if (!file_exists($audioPath)) {
            continue;
        }

        $guestName = $memory['guest_name'] ?: 'Anonymous';
        $safeGuestName = preg_replace('/[^a-zA-Z0-9\-_ ]/', '', $guestName);
        $safeMomentTitle = preg_replace('/[^a-zA-Z0-9\-_ ]/', '', $momentTitle);
        $baseFilename = trim($safeGuestName) . ' - ' . trim($safeMomentTitle);

        if (isset($filenameCounts[$baseFilename])) {
            $filenameCounts[$baseFilename]++;
            $entryFilename = $baseFilename . ' (' . $filenameCounts[$baseFilename] . ').mp3';
        } else {
            $filenameCounts[$baseFilename] = 1;
            $entryFilename = $baseFilename . '.mp3';
        }

        // Add pre-encoded MP3 directly to zip
        $zip->addFile($audioPath, $entryFilename);
    }

    $zip->close();

    if (!file_exists($zipFilename)) {
        die("Failed to generate ZIP file.");
    }

    $safeDownloadTitle = preg_replace('/[^a-zA-Z0-9\-_ ]/', '', $momentTitle);
    $downloadFilename = 'Memories - ' . trim($safeDownloadTitle) . '.zip';

    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . addslashes($downloadFilename) . '"');
    header('Content-Length: ' . filesize($zipFilename));
    header('Cache-Control: no-cache');

    if (ob_get_level()) {
        ob_end_clean();
    }
    flush();

    readfile($zipFilename);
    @unlink($zipFilename);
    exit;

} catch (PDOException $e) {
    die("Database error occurred.");
}
