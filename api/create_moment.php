<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonErr('Method not allowed', 405);
}

$input = $_POST;
if (empty($input)) {
    $raw = file_get_contents('php://input');
    if (!empty($raw)) {
        $input = json_decode($raw, true) ?? [];
    }
}

$title = trim($input['title'] ?? '');
$category = trim($input['category'] ?? '');
$description = trim($input['description'] ?? '');
$startTime = trim($input['start_time'] ?? '');
$endTime = trim($input['end_time'] ?? '');

if ($title === '' || mb_strlen($title) > 120) {
    jsonErr('Invalid title', 400);
}

$validCategories = ['graduation', 'wedding', 'birthday', 'farewell', 'other'];
if (!in_array($category, $validCategories, true)) {
    jsonErr('Invalid category', 400);
}

$format = 'Y-m-d H:i:s';
$startDt = DateTime::createFromFormat($format, $startTime);
$endDt = DateTime::createFromFormat($format, $endTime);

if (!$startDt || $startDt->format($format) !== $startTime) {
    jsonErr('Invalid start_time format', 400);
}
if (!$endDt || $endDt->format($format) !== $endTime) {
    jsonErr('Invalid end_time format', 400);
}

if ($startDt >= $endDt) {
    jsonErr('end_time must be after start_time', 400);
}

$db = get_db();
$slug = generateSlug($db);

// Generate Host Key (e.g. ABCD-EFGH)
$hostKey = strtoupper(substr(bin2hex(random_bytes(4)), 0, 4) . '-' . substr(bin2hex(random_bytes(4)), 0, 4));

$baseDir = rtrim(UPLOAD_BASE, '/\\') . DIRECTORY_SEPARATOR . $slug;
if (!mkdir($baseDir . DIRECTORY_SEPARATOR . 'audio', 0755, true) ||
    !mkdir($baseDir . DIRECTORY_SEPARATOR . 'covers', 0755, true)) {
    jsonErr('Could not create storage directories', 500);
}

$stmt = $db->prepare("
    INSERT INTO moments (slug, title, category, description, start_time, end_time, host_key) 
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

$stmt->execute([
    $slug,
    $title,
    $category,
    $description,
    $startTime,
    $endTime,
    $hostKey
]);

$shareUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . getBasePath() . '/moment.php?slug=' . $slug;

jsonOk([
    'slug' => $slug,
    'share_url' => $shareUrl,
    'host_key' => $hostKey
]);
