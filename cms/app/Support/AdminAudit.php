<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class AdminAudit
{
    public static function log(int $adminId, string $action, string $targetType, ?int $targetId, string $details = ''): void
    {
        try {
            if (! Schema::connection('holodb')->hasTable('phpretro_admin_action_log')) {
                return;
            }

            DB::connection('holodb')->table('phpretro_admin_action_log')->insert([
                'admin_id' => $adminId,
                'action_type' => $action,
                'target_type' => $targetType,
                'target_id' => $targetId,
                'details' => $details,
                'ip' => (string) (request()->ip() ?? ''),
                'created_at' => time(),
            ]);
        } catch (\Throwable) {
        }
    }
}
