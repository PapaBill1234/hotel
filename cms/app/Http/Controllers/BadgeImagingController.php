<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BadgeImagingController extends Controller
{
    public function __invoke(Request $request, ?string $badge = null): Response
    {
        $code = $badge ?? (string) $request->query('badge', '');
        $code = preg_replace('/\.gif$/i', '', $code) ?? '';
        if ($code === '' || preg_match('/^[a-zA-Z0-9]+$/', $code) !== 1) {
            abort(400);
        }

        $root = realpath(base_path('../../legacy/habbo-imaging'))
            ?: realpath('/workspace/hotel/legacy/habbo-imaging');
        if ($root === false) {
            abort(404);
        }

        $cache = $root.'/../cache/badges/'.$code.'.gif';
        if (is_file($cache)) {
            return response(file_get_contents($cache), 200, ['Content-Type' => 'image/gif']);
        }

        $base = $root.'/badges/base/base.gif';
        if (! function_exists('imagecreatefromgif') || ! is_file($base)) {
            $fallback = is_file($base) ? $base : $root.'/badges/templates/none.gif';
            abort_unless(is_file($fallback), 404);

            return response(file_get_contents($fallback), 200, ['Content-Type' => 'image/gif']);
        }

        $im = @imagecreatefromgif($base);
        abort_unless($im, 500);
        imagealphablending($im, true);
        imagesavealpha($im, true);

        $stripped = str_replace(['b', 'X'], '', $code);
        $layers = explode('s', $stripped);
        foreach ($layers as $i => $layer) {
            if ($layer === '' || $i === 0 && strlen($layer) < 2) {
                continue;
            }
            $parts = str_split($layer, 2);
            $tpl = $parts[0] ?? '';
            $file = $root.'/badges/templates/'.$tpl.'.gif';
            if (! is_file($file)) {
                continue;
            }
            $lay = @imagecreatefromgif($file);
            if (! $lay) {
                continue;
            }
            imagecopy($im, $lay, 0, 0, 0, 0, imagesx($lay), imagesy($lay));
            imagedestroy($lay);
        }

        ob_start();
        imagegif($im);
        $gif = ob_get_clean();
        imagedestroy($im);

        return response($gif, 200, ['Content-Type' => 'image/gif']);
    }
}
