<?php

namespace Tests\Feature;

use Tests\TestCase;

class VisualContractTest extends TestCase
{
    public function test_web_gallery_is_not_in_public(): void
    {
        $this->assertDirectoryDoesNotExist(public_path('web-gallery'));
        $this->assertFileDoesNotExist(public_path('web-gallery'));
    }

    public function test_vite_config_does_not_bundle_gallery(): void
    {
        $vite = file_get_contents(base_path('vite.config.ts'));
        $this->assertNotFalse($vite);

        $codeLines = array_filter(
            explode("\n", $vite),
            fn (string $line): bool => ! preg_match('/^\s*(\/\/|\*)/', $line)
        );

        $this->assertStringNotContainsString(
            'web-gallery',
            implode("\n", $codeLines)
        );
    }

    public function test_legacy_skin_uses_frozen_gallery_urls(): void
    {
        $skin = file_get_contents(resource_path('js/layouts/legacy-skin.tsx'));
        $this->assertNotFalse($skin);
        $this->assertStringContainsString('/web-gallery/v2/styles/style.css', $skin);
        $this->assertStringContainsString('/web-gallery/static/js/common.js', $skin);
    }

    public function test_sys_connection_is_documented_as_default(): void
    {
        $this->assertArrayHasKey('sys', config('database.connections'));
        $this->assertArrayHasKey('holodb', config('database.connections'));

        $example = file_get_contents(base_path('.env.example'));
        $this->assertNotFalse($example);
        $this->assertStringContainsString('DB_CONNECTION=sys', $example);
        $this->assertStringContainsString('SESSION_COOKIE=hotel_session', $example);
        $this->assertStringContainsString('LEGACY_COOKIE_NAME=PHPSESSID', $example);
    }
}
