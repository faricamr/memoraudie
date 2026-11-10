<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonErr('Method Not Allowed', 405);
}

if (!isset($_GET['slug']) || trim($_GET['slug']) === '') {
    jsonErr('Slug is required', 400);
}

$slug = trim($_GET['slug']);

try {
    $db = get_db();

    $stmt = $db->prepare("SELECT * FROM moments WHERE slug = ?");
    $stmt->execute([$slug]);
    $moment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$moment) {
        jsonErr('Moment not found', 404);
    }

    $state = computeState($moment['start_time'], $moment['end_time']);

    $stmtMemories = $db->prepare("SELECT * FROM memories WHERE moment_id = ? ORDER BY created_at ASC");
    $stmtMemories->execute([$moment['id']]);
    $memories = $stmtMemories->fetchAll(PDO::FETCH_ASSOC);

    foreach ($memories as &$m) {
        $m['cover_url'] = buildCoverPath($slug, $m['cover_filename']);
        $m['audio_url'] = buildAudioPath($slug, $m['audio_filename']);
        $m['stickers_data'] = !empty($m['stickers']) ? json_decode($m['stickers'], true) : [];
    }

    jsonOk([
        'moment' => $moment,
        'state' => $state,
        'memories' => $memories
    ]);

} catch (PDOException $e) {
    jsonErr('Database error', 500);
}
