<?php
// Check Auto-Loader
if ( !is_dir(__DIR__.'/vendor') || !is_readable(__DIR__.'/vendor/autoload.php')) {
    die('vendor/autoload.php could not be read');
}

// Apply Auto-Loader
require_once __DIR__.'/vendor/autoload.php';
use Google\CloudFunctions\FunctionsFramework;

// Register functions with the Functions Framework.
// https://console.cloud.google.com/functions/list
FunctionsFramework::http(        'on_https', 'Quickstart\\CloudFunctions::on_https');
FunctionsFramework::cloudEvent( 'on_pubsub', 'Quickstart\\CloudFunctions::on_pubsub');
FunctionsFramework::cloudEvent(    'on_gcs', 'Quickstart\\CloudFunctions::on_gcs');

