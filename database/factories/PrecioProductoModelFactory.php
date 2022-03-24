<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PrecioProductoModelFactory extends Factory
{
    public function definition()
    {
        return [
            'precio' => $this->faker->randomFloat($nbMaxDecimals = NULL, $min = 10, $max = 120)
        ];
    }
}
