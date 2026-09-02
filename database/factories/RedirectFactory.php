<?php

namespace Database\Factories;

use App\Seo\Models\Redirect;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Redirect>
 */
class RedirectFactory extends Factory
{
    protected $model = Redirect::class;

    public function definition(): array
    {
        return [
            'from_path' => '/'.fake()->unique()->slug(),
            'to_url' => '/shop',
            'status' => 301,
        ];
    }
}
