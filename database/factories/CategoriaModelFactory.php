<?php

namespace Database\Factories;

use App\Models\CategoriaModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoriaModelFactory extends Factory
{
    //protected $model = CategoriaModel::class;

    public function definition()
    {
       return [
           'nombre' => $this->faker->randomElement(['Cereales','Verduras','Frurtas','Panes de molde','Lácteos'])
        ];
    }
}
