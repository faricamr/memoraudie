<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonErr('Method not allowed', 405);
}

$input = getRequestInput();
$slug = trim($input['slug'] ?? '');
$hostKey = trim($input['host_key'] ?? '');

if ($slug === '' || $hostKey === '') {
    jsonErr('Missing parameters', 400);
}

$db = get_db();
$moment = verifyHost($db, $slug, $hostKey);

$stmtUpd = $db->prepare("UPDATE moments SET end_time = NOW() WHERE id = ?");
$stmtUpd->execute([$moment['id']]);

jsonOk(['message' => 'Moment closed early']);
