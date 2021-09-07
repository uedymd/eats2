<?php

namespace Database\Factories;

use App\Models\Rakuten;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RakutenFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Rakuten::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => 'タイトル',
            'keyword' => 'キーワード',
            'genre' => 'ジャンル',
            'genre_id' => Str::random(10),
            'ng_keyword' => Str::words(3),
            'ng_url' => Str::words(3),
            'price_max' => 1000,
            'price_min' => 600,
            'status' => 1,
        ];
    }
}
