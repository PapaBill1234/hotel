<?php

namespace Tests\Feature;

use App\Support\HolodbWriteGuard;
use RuntimeException;
use Tests\TestCase;

class HolodbReadOnlyTest extends TestCase
{
    public function test_select_is_allowed(): void
    {
        $this->assertTrue(HolodbWriteGuard::isReadOnly('select 1'));
        $this->assertTrue(HolodbWriteGuard::isReadOnly('SELECT * FROM phpretro_users LIMIT 1'));
        $this->assertTrue(HolodbWriteGuard::isReadOnly('SHOW TABLES'));
    }

    public function test_writes_are_rejected(): void
    {
        $this->expectException(RuntimeException::class);
        HolodbWriteGuard::assertReadOnly('INSERT INTO users (id) VALUES (1)');
    }

    public function test_ddl_is_rejected(): void
    {
        $this->assertFalse(HolodbWriteGuard::isReadOnly('DROP TABLE users'));
        $this->assertFalse(HolodbWriteGuard::isReadOnly('ALTER TABLE users ADD COLUMN x INT'));
        $this->assertFalse(HolodbWriteGuard::isReadOnly('UPDATE users SET name = "x"'));
        $this->assertFalse(HolodbWriteGuard::isReadOnly('DELETE FROM users'));
    }

    public function test_comment_wrapped_write_is_still_caught(): void
    {
        $this->assertFalse(HolodbWriteGuard::isReadOnly('/* select 1 */ DELETE FROM users'));
    }
}
