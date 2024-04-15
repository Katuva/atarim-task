<?php

namespace Database\Factories;

use App\Models\Url;
use App\Services\UrlShortener\UrlShortener;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class UrlFactory extends Factory
{
    protected $model = Url::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'url' => $this->faker->url(),
            'code' => $this->faker->unique()->slug(),
        ];
    }
}
