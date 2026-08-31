<?php

namespace Tests\Feature;

use App\Models\Buku;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Beranda publik dapat diakses tanpa login.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        Buku::factory()->count(3)->create();

        $this->get('/')->assertStatus(200);
    }
}
