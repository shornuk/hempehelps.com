<?php

namespace Tests\Feature;

use App\Seo\Models\Redirect;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RedirectsTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_redirects_are_honoured(): void
    {
        Redirect::factory()->create([
            'from_path' => '/blogs/guides',
            'to_url' => '/guides',
        ]);

        $this->get('/blogs/guides?utm_source=shopify')
            ->assertRedirect('/guides?utm_source=shopify');
    }
}
