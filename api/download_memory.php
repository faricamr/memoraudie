<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    die("Method not allowed.");
}

$slug = trim($_GET['slug'] ?? '');
$memoryId = (int)($_GET['memory_id'] ?? 0);

if (!$slug || !$memoryId) {
    die("Invalid request parameters.");
}

try {
    $db = get_db();

    // Fetch moment and memory data
    $stmt = $db->prepare("
        SELECT mo.title AS moment_title,
               m.id, m.guest_name, m.audio_filename, m.created_at
        FROM memories m
        JOIN moments mo ON m.moment_id = mo.id
        WHERE mo.slug = ? AND m.id = ?
    ");
    $stmt->execute([$slug, $memoryId]);
    $memory = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$memory) {
        die("Memory not found.");
    }

    $audioPath = UPLOAD_BASE . '/' . $slug . '/audio/' . $memory['audio_filename'];

    if (!file_exists($audioPath)) {
        die("Audio file missing.");
    }

    $guestName = $memory['guest_name'] ?: 'Anonymous';
    $momentTitle = $memory['moment_title'] ?: 'Memoraudie';

    $safeGuestName = preg_replace('/[^a-zA-Z0-9\-_ ]/', '', $guestName);
    $safeMomentTitle = preg_replace('/[^a-zA-Z0-9\-_ ]/', '', $momentTitle);
    $outputFilename = trim($safeGuestName) . ' - ' . trim($safeMomentTitle) . '.mp3';

    // Stream the pre-encoded MP3 file directly
    header('Content-Type: audio/mpeg');
    header('Content-Disposition: attachment; filename="' . addslashes($outputFilename) . '"');
    header('Content-Length: ' . filesize($audioPath));
    header('Cache-Control: no-cache');
    
    if (ob_get_level()) {
        ob_end_clean();
    }
    flush();

    readfile($audioPath);
    exit;

} catch (PDOException $e) {
    die("Database error occurred.");
}
