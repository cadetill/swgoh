<?php

$now = new DateTimeImmutable();
foreach (glob('./cache/*_*_*') as $filePath) {
    $filename = basename($filePath);
    $matches = [];
    if (preg_match('/(.*)_(.*)_(.*)/', $filename, $matches)) {
        var_dump($matches);
        [ $_, $type, $key, $timestamp ] = $matches;
        $timestampDate = (new DateTimeImmutable())->setTimestamp($timestamp);
        $stillValid = $timestampDate > $now;
        if (!$stillValid) {
            unlink($filePath);
        }
    }
}

http_response_code(200);
