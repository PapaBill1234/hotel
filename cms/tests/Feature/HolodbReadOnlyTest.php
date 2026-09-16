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

    public function test_phpretro_dml_is_allowed(): void
    {
        $this->assertTrue(HolodbWriteGuard::isReadOnly('insert into "phpretro_news" ("title") values (?)'));
        $this->assertTrue(HolodbWriteGuard::isReadOnly('update "phpretro_site_settings" set "setting_value" = ? where "setting_key" = ?'));
        $this->assertTrue(HolodbWriteGuard::isReadOnly('delete from phpretro_faq where id = ?'));
        $this->assertTrue(HolodbWriteGuard::isReadOnly('INSERT INTO phpretro_helpdesk_tickets (subject) VALUES (?)'));
    }

    public function test_phpretro_ddl_is_still_rejected(): void
    {
        $this->assertFalse(HolodbWriteGuard::isReadOnly('DROP TABLE phpretro_news'));
        $this->assertFalse(HolodbWriteGuard::isReadOnly('ALTER TABLE phpretro_faq ADD COLUMN x INT'));
        $this->assertFalse(HolodbWriteGuard::isReadOnly('CREATE TABLE phpretro_x (id INT)'));
    }

    public function test_users_settings_insert_is_rejected(): void
    {
        $this->assertFalse(HolodbWriteGuard::isReadOnly('INSERT INTO users_settings (user_id) VALUES (1)'));
    }
}
