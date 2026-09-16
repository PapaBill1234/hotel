<?php

namespace Tests\Feature;

use Tests\TestCase;

class HotelCapturedPagesTest extends TestCase
{
    public function test_papers_privacy_renders(): void
    {
        $this->get('/papers/privacy')->assertOk()->assertSee('Privacy Policy', false);
    }

    public function test_papers_disclaimer_renders(): void
    {
        $this->get('/papers/disclaimer')->assertOk()->assertSee('Disclaimer', false);
    }

    public function test_help_faq_renders(): void
    {
        $this->get('/help')->assertOk()->assertSee('Can\'t find an answer', false);
    }

    public function test_tag_search_is_honest(): void
    {
        $this->get('/tag')->assertOk()->assertSee('PolarIS has no tags table', false);
    }

    public function test_collectables_renders(): void
    {
        $this->get('/credits/collectables')->assertOk()->assertSee('Current Collectable', false);
    }

    public function test_housekeeping_login_renders(): void
    {
        $this->get('/housekeeping')->assertOk()->assertSee('Please log in', false);
    }

    public function test_tryout_renders(): void
    {
        $this->get('/credits/club/tryout')->assertOk()->assertSee('Test Wardrobe', false);
    }

    public function test_intermediate_renders(): void
    {
        $this->get('/intermediate')->assertOk()->assertSee('Choose your destination', false);
    }

    public function test_email_verify_is_honest_without_token(): void
    {
        $this->get('/email')->assertOk()->assertSee('The verification code is invalid', false);
    }

    public function test_client_error_renders(): void
    {
        $this->get('/client_error')->assertOk()->assertSee('Oops', false);
    }

    public function test_cache_check_returns_true(): void
    {
        $this->get('/cacheCheck')->assertOk()->assertSee('true', false);
    }

    public function test_club_subscribe_requires_sign_in(): void
    {
        $this->post('/habboclub/habboclub_subscribe')->assertRedirect('/');
    }

    public function test_trax_is_unavailable(): void
    {
        $this->get('/trax/song/1')->assertStatus(501)->assertSee('Trax', false);
    }

    public function test_mod_localizations_renders(): void
    {
        $this->get('/mod/localizations')->assertOk()->assertSee('success', false);
    }
}
