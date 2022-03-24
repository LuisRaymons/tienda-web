<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VentaModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'factura' => $this->faker->bothify('Facture-###???'),
            'impuesto' => 0.16,
            'precio_total' => $this->faker->randomFloat($nbMaxDecimals = NULL, $min = 10, $max = 6000),
            'created_at' => $this->faker->dateTimeBetween($startDate = '-3 month',$endDate = 'now +6 month')
        ];
    }
}
