<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Tests\TestCase;

class SetupCredentialsTest extends TestCase
{
    public function test_web_setup_is_unavailable_without_a_configured_key(): void
    {
        config()->set('app.setup_key', null);

        $this->get('/setup?key=anything')
            ->assertServiceUnavailable()
            ->assertJson(['error' => 'Web setup is not configured.']);
    }

    public function test_web_setup_requires_an_admin_password_before_running(): void
    {
        config()->set('app.setup_key', 'a-configured-setup-key');
        config()->set('app.admin_password', null);

        $this->get('/setup?key=a-configured-setup-key')
            ->assertServiceUnavailable()
            ->assertJson(['error' => 'Set TRIO_ADMIN_PASSWORD before running web setup.']);
    }

    public function test_database_seeder_requires_an_admin_password(): void
    {
        config()->set('app.admin_password', null);
        $this->expectException(\RuntimeException::class);

        (new DatabaseSeeder)->run();
    }
}
