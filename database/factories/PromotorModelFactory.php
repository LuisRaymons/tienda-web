<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PromotorModelFactory extends Factory
{
    public function definition()
    {
      return [
          'nombre' => $this->faker->name,
          'direccion' => $this->faker->address,
          'telefono' => $this->faker->e164PhoneNumber,
          'sitioWeb' => $this->faker->url,
          'img' => $this->faker->imageUrl($width = 120, $height = 300)
      ];
    }
}
