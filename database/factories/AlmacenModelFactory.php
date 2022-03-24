<?php

namespace Database\Factories;

use App\Models\AlmacenModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlmacenModelFactory extends Factory
{
    //protected $model = AlmacenModel::class;

    public function definition()
    {
        return [
             'entrada' => rand(10, 999),
             'salida' => rand(1,20),
             'stock' => rand(1,30),
             'id_user' => rand(1, 9),
             'id_producto'=> rand(1, 20)
         ];
    }
}
