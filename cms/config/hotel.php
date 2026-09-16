<?php

return [
    'legacy_url' => env('LEGACY_URL', 'http://host.docker.internal:8080'),
    'gallery_probe' => env('LEGACY_GALLERY_PROBE', '/web-gallery/v2/styles/style.css'),
    'legacy_cookie' => env('LEGACY_COOKIE_NAME', 'PHPSESSID'),
    'session_cookie' => env('SESSION_COOKIE', 'hotel_session'),
    'gallery_path' => env('HOTEL_GALLERY_PATH', dirname(__DIR__, 2).'/legacy/web-gallery'),
    'shortname' => env('HOTEL_SHORTNAME', 'PHPRetro'),
    'imager' => env('HOTEL_IMAGER_URL', 'https://www.habbo.com/habbo-imaging/avatarimage'),
    'hotel_online' => env('HOTEL_ONLINE', true),
];
