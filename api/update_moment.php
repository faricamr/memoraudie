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
$title = trim($input['title'] ?? '');
$startTime = trim($input['start_time'] ?? '');
$endTime = trim($input['end_time'] ?? '');

if ($slug === '' || $hostKey === '') {
    jsonErr('Missing parameters', 400);
}

if ($title === '' || mb_strlen($title) > 120) {
    jsonErr('Invalid title', 400);
}

$format = 'Y-m-d H:i:s';
$startDt = DateTime::createFromFormat($format, $startTime);
$endDt = DateTime::createFromFormat($format, $endTime);

if (!$startDt || $startDt->format($format) !== $startTime || !$endDt || $endDt->format($format) !== $endTime || $startDt >= $endDt) {
    jsonErr('Invalid date range or format', 400);
}

$db = get_db();
$moment = verifyHost($db, $slug, $hostKey);

$stmtUpd = $db->prepare("UPDATE moments SET title = ?, start_time = ?, end_time = ? WHERE id = ?");
$stmtUpd->execute([$title, $startTime, $endTime, $moment['id']]);

jsonOk(['message' => 'Moment updated']);
