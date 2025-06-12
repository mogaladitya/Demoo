<?php
require_once 'functions.php';

// Fetch and format XKCD comic data
$xkcdData = fetchAndFormatXKCDData();

if ($xkcdData) {
    // Send XKCD updates to all registered emails
    sendXKCDUpdatesToSubscribers($xkcdData);
} else {
    error_log("Failed to fetch XKCD comic data.");
}