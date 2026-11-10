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
    jsonErr('Missing slug or host_key', 400);
}

$db = get_db();
$stmt = $db->prepare("SELECT id FROM moments WHERE slug = ? AND host_key = ?");
$stmt->execute([$slug, $hostKey]);

jsonOk(['valid' => (bool)$stmt->fetch()]);
