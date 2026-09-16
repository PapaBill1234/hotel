<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class Hotel
{
    public static function shortname(): string
    {
        return self::setting('site_shortname', (string) config('hotel.shortname', 'PHPRetro'));
    }

    public static function setting(string $key, string $default = ''): string
    {
        try {
            if (! Schema::connection('holodb')->hasTable('phpretro_site_settings')) {
                return $default;
            }

            $value = DB::connection('holodb')->table('phpretro_site_settings')
                ->where('setting_key', $key)
                ->value('setting_value');

            return $value === null || $value === '' ? $default : (string) $value;
        } catch (\Throwable) {
            return $default;
        }
    }

    public static function onlineCount(): int
    {
        try {
            return (int) DB::connection('holodb')->table('users')->where('online', '1')->count();
        } catch (\Throwable) {
            return 0;
        }
    }

    public static function faqFooterLinks(): string
    {
        try {
            if (! Schema::connection('holodb')->hasTable('phpretro_faq')) {
                return '';
            }

            $rows = DB::connection('holodb')->table('phpretro_faq')
                ->where('active', 1)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(['id', 'question']);

            $html = '';
            foreach ($rows as $row) {
                $html .= ' | <a href="/help/'.(int) $row->id.'" target="_new">'.e($row->question).'</a>';
            }

            return $html;
        } catch (\Throwable) {
            return '';
        }
    }

    public static function randomOrderSql(): string
    {
        return DB::connection('holodb')->getDriverName() === 'sqlite' ? 'RANDOM()' : 'RAND()';
    }

    public static function avatarUrl(string $look, string $style = 'b,3,3,sml,1,0'): string
    {
        $parts = explode(',', $style);

        return rtrim((string) config('hotel.imager'), '?').'?figure='.rawurlencode($look)
            .'&size='.($parts[0] ?? 'b')
            .'&direction='.($parts[1] ?? '3')
            .'&head_direction='.($parts[2] ?? '3')
            .'&gesture='.($parts[3] ?? 'sml')
            .'&frame='.($parts[4] ?? '1');
    }

    public static function newsSlug(string $title): string
    {
        $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $title) ?? ''));

        return trim($slug, '-') ?: 'news';
    }

    public static function clientHandoff(string $title, string $body)
    {
        $html = '<div class="habblet-client-handoff">'
            .'<p><b>'.e($title).'</b></p>'
            .'<p>'.e($body).'</p>'
            .'<p><a href="/client" class="new-button" target="client" onclick="if(typeof HabboClient!=\'undefined\'){HabboClient.openOrFocus(this);} return false;"><b>Open hotel</b><i></i></a></p>'
            .'</div>';

        return response($html)->header('X-PHPRetro-Feature', 'client-handoff');
    }

    public static function unavailable(string $message, int $status = 501)
    {
        return response('<p class="habblet-unavailable">'.e($message).'</p>', $status)
            ->header('X-PHPRetro-Feature', 'unavailable');
    }

    /**
     * @return array<int, string>
     */
    public static function userTags(int $userId): array
    {
        try {
            if (! Schema::connection('holodb')->hasTable('users_settings')) {
                return [];
            }
            $tags = DB::connection('holodb')->table('users_settings')->where('user_id', $userId)->value('tags');

            return array_values(array_unique(array_filter(explode(';', (string) $tags), static fn (string $tag): bool => $tag !== '')));
        } catch (\Throwable) {
            return [];
        }
    }

    public static function tagCount(string $tag): int
    {
        if ($tag === '' || str_contains($tag, ';')) {
            return 0;
        }
        try {
            if (! Schema::connection('holodb')->hasTable('users_settings')) {
                return 0;
            }

            return (int) DB::connection('holodb')->table('users_settings as s')
                ->join('users as u', 'u.id', '=', 's.user_id')
                ->whereRaw("INSTR(';' || s.tags || ';', ?) > 0", [';'.$tag.';'])
                ->count();
        } catch (\Throwable) {
            return 0;
        }
    }
}
