<?php

return [
    'legacy_url' => env('LEGACY_URL', 'http://host.docker.internal:8080'),
    'gallery_probe' => env('LEGACY_GALLERY_PROBE', '/web-gallery/v2/styles/style.css'),
    'legacy_cookie' => env('LEGACY_COOKIE_NAME', 'PHPSESSID'),
    'session_cookie' => env('SESSION_COOKIE', 'hotel_session'),
];
