<?php
date_default_timezone_set('Asia/Jakarta');

// Database Configuration
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'memoraudie_db');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
define('DB_PORT', getenv('DB_PORT') ?: '3306');

// External Dependencies
$localFfmpeg = 'D:\\punya farica\\Programs\\ffmpeg-9.0.2-essentials_build\\bin\\ffmpeg.exe';
define('FFMPEG_BIN', file_exists($localFfmpeg) ? $localFfmpeg : 'ffmpeg');

// Paths & URLs
define('UPLOAD_BASE', __DIR__ . '/../uploads');
define('TEMP_BASE', __DIR__ . '/../uploads/temp');

// Application Limits
define('MAX_DURATION_SEC', 180); // 3 minutes
define('MAX_AUDIO_BYTES', 26214400); // 25 MB
define('MAX_COVER_BYTES', 5242880); // 5 MB

// end config



