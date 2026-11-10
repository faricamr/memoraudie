<?php
require_once __DIR__ . '/config.php';

function jsonOk(array $data, int $code = 200): void {
    header('Content-Type: application/json');
    http_response_code($code);
    echo json_encode(['success' => true, 'data' => $data]);
    exit;
}

function jsonErr(string $message, int $code = 400): void {
    header('Content-Type: application/json');
    http_response_code($code);
    echo json_encode(['success' => false, 'error' => $message]);
    exit;
}

function generateSlug(PDO $db): string {
    $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $attempts = 0;
    
    while ($attempts < 10) {
        $slug = '';
        for ($i = 0; $i < 8; $i++) {
            $slug .= $characters[random_int(0, strlen($characters) - 1)];
        }
        
        $stmt = $db->prepare("SELECT id FROM moments WHERE slug = ?");
        $stmt->execute([$slug]);
        if (!$stmt->fetch()) {
            return $slug;
        }
        $attempts++;
    }
    
    throw new RuntimeException("Could not generate a unique slug after 10 attempts.");
}

function computeState(string $startTimeStr, string $endTimeStr): string {
    $now = new DateTime();
    $start = new DateTime($startTimeStr);
    $end = new DateTime($endTimeStr);
    
    if ($now < $start) {
        return 'upcoming';
    } elseif ($now > $end) {
        return 'closed';
    } else {
        return 'active';
    }
}

function getBasePath(): string {
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    if (str_ends_with($scriptDir, '/api')) {
        $scriptDir = substr($scriptDir, 0, -4);
    }
    return rtrim($scriptDir, '/');
}

function buildAudioPath(string $slug, string $filename): string {
    return getBasePath() . '/uploads/' . urlencode($slug) . '/audio/' . urlencode($filename);
}

function buildCoverPath(string $slug, string $filename): string {
    return getBasePath() . '/uploads/' . urlencode($slug) . '/covers/' . urlencode($filename);
}

function getRequestInput(): array {
    if (!empty($_POST)) return $_POST;
    $raw = file_get_contents('php://input');
    return !empty($raw) ? (json_decode($raw, true) ?? []) : [];
}

function verifyHost(PDO $db, string $slug, string $hostKey): array {
    $stmt = $db->prepare("SELECT id, title, host_key FROM moments WHERE slug = ? AND host_key = ?");
    $stmt->execute([$slug, $hostKey]);
    $moment = $stmt->fetch();
    if (!$moment) {
        jsonErr('Invalid host key', 403);
    }
    return $moment;
}
