<?php

namespace App\Support;

use RuntimeException;

final class HolodbWriteGuard
{
    /**
     * Statements that must never run on holodb in Phase 2.
     * SELECT / SHOW / DESCRIBE / EXPLAIN / SET are allowed.
     */
    private const WRITE_PATTERN = '/\b(insert|update|delete|replace|alter|drop|create|truncate|rename|grant|revoke|load|lock|unlock|call|do|handler|import|optimize|analyze|repair|flush|install|uninstall)\b/i';

    public static function assertReadOnly(string $sql): void
    {
        $stripped = self::stripComments($sql);

        if (preg_match(self::WRITE_PATTERN, $stripped) === 1) {
            throw new RuntimeException(
                'holodb is read-only in Phase 2; refused SQL: '.$sql
            );
        }
    }

    public static function isReadOnly(string $sql): bool
    {
        return preg_match(self::WRITE_PATTERN, self::stripComments($sql)) !== 1;
    }

    private static function stripComments(string $sql): string
    {
        $stripped = preg_replace('/\/\*.*?\*\//s', ' ', $sql) ?? $sql;
        $stripped = preg_replace('/--[^\n]*+/', ' ', $stripped) ?? $stripped;

        return preg_replace('/#[^\n]*+/', ' ', $stripped) ?? $stripped;
    }
}
