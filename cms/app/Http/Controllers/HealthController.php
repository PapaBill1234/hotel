<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Throwable;

class HealthController extends Controller
{
    /**
     * sys + holodb ping + Redis 0–3. Used by the edge after /up.
     * Does not replace /up (liveness). A holodb outage returns 503 here only.
     */
    public function ready(): JsonResponse
    {
        $checks = [
            'sys' => $this->pingDatabase('sys'),
            'holodb' => $this->pingDatabase('holodb'),
            'redis.cache' => $this->pingRedis('cache'),
            'redis.session' => $this->pingRedis('session'),
            'redis.queue' => $this->pingRedis('default'),
            'redis.flags' => $this->pingRedis('flags'),
        ];

        $ok = collect($checks)->every(fn (array $row) => $row['ok'] === true);

        return response()->json([
            'status' => $ok ? 'ready' : 'degraded',
            'phase' => 2,
            'checks' => $checks,
        ], $ok ? 200 : 503);
    }

    /**
     * XAMPP / gallery dependency. A failure here must not fail /up.
     */
    public function legacy(): JsonResponse
    {
        $url = rtrim((string) config('hotel.legacy_url'), '/').config('hotel.gallery_probe');

        try {
            $response = Http::timeout(3)->connectTimeout(2)->get($url);
            $ok = $response->successful();

            return response()->json([
                'status' => $ok ? 'ok' : 'down',
                'url' => $url,
                'http' => $response->status(),
            ], $ok ? 200 : 503);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'down',
                'url' => $url,
                'error' => $e->getMessage(),
            ], 503);
        }
    }

    /**
     * @return array{ok: bool, detail: string}
     */
    private function pingDatabase(string $connection): array
    {
        try {
            DB::connection($connection)->select('select 1 as ok');

            return ['ok' => true, 'detail' => 'select 1'];
        } catch (Throwable $e) {
            return ['ok' => false, 'detail' => $e->getMessage()];
        }
    }

    /**
     * @return array{ok: bool, detail: string}
     */
    private function pingRedis(string $connection): array
    {
        try {
            $pong = Redis::connection($connection)->ping();

            return ['ok' => $pong !== false, 'detail' => is_string($pong) ? $pong : 'PONG'];
        } catch (Throwable $e) {
            return ['ok' => false, 'detail' => $e->getMessage()];
        }
    }
}
