<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
    }

    public function test_customers_cannot_visit_admin(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_staff_can_visit_admin(): void
    {
        $this->actingAs(User::factory()->staff()->create())
            ->get('/admin')
            ->assertOk()
            ->assertSee('HEMPEhelps admin');
    }
}
