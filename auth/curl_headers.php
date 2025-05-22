<?php

function getCurlHeaders(string $json_data): array {
    return [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($json_data),
        'X-API-KEY: API_KEY_EDUARDO_222' 
    ];
}
