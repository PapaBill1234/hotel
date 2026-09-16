<?php

namespace App\Support;

final class PolarisUser
{
    public function __construct(
        public readonly int $id,
        public readonly string $username,
        public readonly string $mail,
        public readonly string $motto,
        public readonly string $look,
        public readonly string $gender,
        public readonly int $rank,
        public readonly int $credits,
        public readonly int $pixels,
        public readonly int $lastLogin,
        public readonly string $online,
        public readonly int $accountCreated = 0,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id: (int) $row->id,
            username: (string) $row->username,
            mail: (string) ($row->mail ?? ''),
            motto: (string) ($row->motto ?? ''),
            look: (string) ($row->look ?? ''),
            gender: (string) ($row->gender ?? 'M'),
            rank: (int) ($row->rank ?? 1),
            credits: (int) ($row->credits ?? 0),
            pixels: (int) ($row->pixels ?? 0),
            lastLogin: (int) ($row->last_login ?? 0),
            online: (string) ($row->online ?? '0'),
            accountCreated: (int) ($row->account_created ?? 0),
        );
    }

    public function isStaff(): bool
    {
        return $this->rank > 4;
    }

    public function toSession(): array
    {
        return ['id' => $this->id, 'username' => $this->username];
    }
}
