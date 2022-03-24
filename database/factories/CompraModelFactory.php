<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CompraModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'folio' => $this->faker->bothify('Facture-###???'),
            'cantidad_stock' => $this->faker->randomDigit($prod = NULL, $min=4, $max=100),
            'precio_total' => $this->faker->randomFloat($nbMaxDecimals = NULL, $min = 10, $max = 6000),
            'img' => $this->faker->imageUrl($width = 120, $height = 300)
        ];
    }
}
