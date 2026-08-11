<?php

// Development server router
$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

$file = __DIR__ . $uri;

// Serve existing files directly
if ($uri !== '/' && file_exists($file) && !is_dir($file)) {
    return false;
}

// Load the application entry point
require_once __DIR__ . '/index.php';
