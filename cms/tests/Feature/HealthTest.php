<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthTest extends TestCase
{
    public function test_up_is_alive(): void
    {
        $this->get('/up')->assertOk();
    }

    public function test_health_ready_route_exists(): void
    {
        $response = $this->getJson('/health/ready');

        $this->assertContains($response->status(), [200, 503]);
        $response->assertJsonStructure([
            'status',
            'phase',
            'checks' => [
                'sys',
                'holodb',
                'redis.cache',
                'redis.session',
                'redis.queue',
                'redis.flags',
            ],
        ]);
        $this->assertSame(2, $response->json('phase'));
    }

    public function test_health_legacy_does_not_take_down_up(): void
    {
        $legacy = $this->getJson('/health/legacy');
        $this->assertContains($legacy->status(), [200, 503]);
        $legacy->assertJsonStructure(['status', 'url']);

        $this->get('/up')->assertOk();
    }
}
