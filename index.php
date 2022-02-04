<?php
require_once __DIR__.'/vendor/autoload.php';
use Google\CloudFunctions\FunctionsFramework;

// Register the function with Functions Framework.
// https://console.cloud.google.com/functions/list
FunctionsFramework::http(        "on_https", "Quickstart\\CloudFunctions::on_https");
FunctionsFramework::cloudEvent( "on_pubsub", "Quickstart\\CloudFunctions::on_pubsub");
FunctionsFramework::cloudEvent(    "on_gcs", "Quickstart\\CloudFunctions::on_gcs");
