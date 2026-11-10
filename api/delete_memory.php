<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonErr('Method not allowed', 405);
}

$input = getRequestInput();
$memoryId = $input['memory_id'] ?? null;
$slug = trim($input['slug'] ?? '');
$hostKey = trim($input['host_key'] ?? '');

if (!$memoryId || $slug === '' || $hostKey === '') {
    jsonErr('Missing parameters', 400);
}

$db = get_db();
$moment = verifyHost($db, $slug, $hostKey);

$stmtMem = $db->prepare("SELECT audio_filename, cover_filename FROM memories WHERE id = ? AND moment_id = ?");
$stmtMem->execute([$memoryId, $moment['id']]);
$memory = $stmtMem->fetch();

if ($memory) {
    $audioPath = UPLOAD_BASE . '/' . $slug . '/audio/' . $memory['audio_filename'];
    $coverPath = UPLOAD_BASE . '/' . $slug . '/covers/' . $memory['cover_filename'];
    
    if (file_exists($audioPath)) @unlink($audioPath);
    if (file_exists($coverPath)) @unlink($coverPath);
    
    $stmtDel = $db->prepare("DELETE FROM memories WHERE id = ?");
    $stmtDel->execute([$memoryId]);
}

jsonOk(['message' => 'Memory deleted']);
