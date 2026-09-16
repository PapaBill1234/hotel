<?php

namespace App\Support;

use RuntimeException;

final class HolodbWriteGuard
{
    /**
     * Statements that must never run on holodb unless allowlisted.
     */
    private const WRITE_PATTERN = '/\b(insert|update|delete|replace|alter|drop|create|truncate|rename|grant|revoke|load|lock|unlock|call|do|handler|import|optimize|analyze|repair|flush|install|uninstall)\b/i';

    /**
     * PolarIS writes verified for the first slice. Anything else is refused.
     * Never allow INSERT into users_settings.
     */
    private const ALLOWED = [
        '/^update\s+users\s+set\s+password\s*=\s*\?\s+where\s+id\s*=\s*\?$/i',
        '/^update\s+users\s+set\s+last_login\s*=\s*\?\s*,\s*last_online\s*=\s*\?\s*,\s*ip_current\s*=\s*\?\s+where\s+id\s*=\s*\?$/i',
        '/^update\s+users\s+set\s+auth_ticket\s*=\s*\?\s+where\s+id\s*=\s*\?$/i',
        '/^update\s+users\s+set\s+motto\s*=\s*\?\s*,\s*look\s*=\s*\?\s*,\s*gender\s*=\s*\?\s+where\s+id\s*=\s*\?$/i',
    ];

    public static function assertReadOnly(string $sql): void
    {
        $stripped = self::normalize($sql);

        if (self::isAllowed($stripped)) {
            return;
        }

        if (preg_match(self::WRITE_PATTERN, $stripped) === 1) {
            throw new RuntimeException(
                'holodb refused SQL (not on the PolarIS write allowlist): '.$sql
            );
        }
    }

    public static function isReadOnly(string $sql): bool
    {
        $stripped = self::normalize($sql);

        return self::isAllowed($stripped) || preg_match(self::WRITE_PATTERN, $stripped) !== 1;
    }

    private static function isAllowed(string $sql): bool
    {
        foreach (self::ALLOWED as $pattern) {
            if (preg_match($pattern, $sql) === 1) {
                return true;
            }
        }

        return self::isWebsiteTableDml($sql);
    }

    /**
     * Website tables (phpretro_*) live on holodb but are Laravel-owned.
     * PolarIS tables stay read-only except the tiny UPDATE allowlist.
     */
    private static function isWebsiteTableDml(string $sql): bool
    {
        if (preg_match('/\b(alter|drop|create|truncate|rename|grant|revoke)\b/i', $sql) === 1) {
            return false;
        }

        return preg_match(
            '/\b(?:insert\s+into|update|delete\s+from|replace\s+into)\s+["\'`]?phpretro_[a-z0-9_]+["\'`]?/i',
            $sql
        ) === 1;
    }

    private static function normalize(string $sql): string
    {
        $stripped = preg_replace('/\/\*.*?\*\//s', ' ', $sql) ?? $sql;
        $stripped = preg_replace('/--[^\n]*+/', ' ', $stripped) ?? $stripped;
        $stripped = preg_replace('/#[^\n]*+/', ' ', $stripped) ?? $stripped;

        return trim(preg_replace('/\s+/', ' ', $stripped) ?? $stripped);
    }
}
