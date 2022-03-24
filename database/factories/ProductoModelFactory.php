<?php

namespace Database\Factories;

use App\Models\ProductoModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoModelFactory extends Factory
{
    //protected $model = ProductoModel::class;

    public function definition()
    {
        return [
           'nombre' => $this->faker->randomElement(['Doritos','Azúcar','Cereales','Flan en polvo','Mayonesa','Miel']),
           'descripcion' => $this->faker->text($maxNbChars = 200),
           'precioPorKilo' => $this->faker->randomElement(['true','false']),
           'img' => $this->faker->imageUrl($width = 120, $height = 300),
           'id_categoria' => rand(1, 5)
         ];
    }
}
