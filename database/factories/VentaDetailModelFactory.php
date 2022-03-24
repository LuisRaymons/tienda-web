<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VentaDetailModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'cantidad' => rand(1,20),
            'precio'  =>  $this->faker->randomFloat($nbMaxDecimals = NULL, $min = 10, $max = 120),
        ];
    }
}
