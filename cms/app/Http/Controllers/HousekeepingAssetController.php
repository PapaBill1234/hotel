<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class HousekeepingAssetController extends Controller
{
    public function __invoke(Request $request, string $path = 'favicon.ico'): BinaryFileResponse
    {
        $root = realpath(base_path('../legacy/housekeeping'));
        abort_unless($root && is_dir($root), 404);

        if ($path !== 'favicon.ico' && ! str_starts_with($path, 'images/')) {
            $path = 'images/'.$path;
        }

        $target = realpath($root.DIRECTORY_SEPARATOR.str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path));
        abort_unless($target && str_starts_with($target, $root) && is_file($target), 404);

        $mime = match (strtolower(pathinfo($target, PATHINFO_EXTENSION))) {
            'css' => 'text/css; charset=UTF-8',
            'js' => 'application/javascript; charset=UTF-8',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'jpg', 'jpeg' => 'image/jpeg',
            'ico' => 'image/x-icon',
            default => 'application/octet-stream',
        };

        return response()->file($target, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
