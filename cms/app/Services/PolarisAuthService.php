<?php

namespace App\Services;

use App\Support\PolarisUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class PolarisAuthService
{
    public function findByUsername(string $username): ?object
    {
        if (! Schema::connection('holodb')->hasTable('users')) {
            return null;
        }

        return DB::connection('holodb')->table('users')->where('username', $username)->first();
    }

    public function findById(int $id): ?PolarisUser
    {
        $row = DB::connection('holodb')->table('users')->where('id', $id)->first();

        return $row ? PolarisUser::fromRow($row) : null;
    }

    public function attempt(string $username, string $password): ?PolarisUser
    {
        $row = $this->findByUsername($username);
        if (! $row) {
            return null;
        }

        $stored = (string) $row->password;
        $ok = password_verify($password, $stored);
        if (! $ok) {
            $legacy = sha1($password.strtolower((string) $row->username));
            if (hash_equals($legacy, $stored)) {
                $ok = true;
                DB::connection('holodb')->update(
                    'UPDATE users SET password = ? WHERE id = ?',
                    [password_hash($password, PASSWORD_DEFAULT), (int) $row->id]
                );
            }
        }

        if (! $ok) {
            return null;
        }

        DB::connection('holodb')->update(
            'UPDATE users SET last_login = ?, last_online = ?, ip_current = ? WHERE id = ?',
            [time(), time(), (string) request()->ip(), (int) $row->id]
        );

        $fresh = $this->findById((int) $row->id);

        return $fresh;
    }

    public function issueAuthTicket(int $userId): string
    {
        $ticket = bin2hex(random_bytes(16));
        DB::connection('holodb')->update(
            'UPDATE users SET auth_ticket = ? WHERE id = ?',
            [$ticket, $userId]
        );

        return $ticket;
    }
}
