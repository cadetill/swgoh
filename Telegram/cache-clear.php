<?php

$cacheDir     = './cache';
$ttlInSeconds = 10800; // seconds

if ($handle = opendir($cacheDir)) {
    while (false !== ( $file = readdir($handle) )) {
        if (is_dir($file)) {
            continue;
        }

        if ($file === '.gitkeep') {
            continue;
        }

        $fileLastModified = filemtime($cacheDir . '/' . $file);
        if (( time() - $fileLastModified ) > $ttlInSeconds) {
            unlink($cacheDir . '/' . $file);
        }
    }
    closedir($handle);
}

http_response_code(200);
