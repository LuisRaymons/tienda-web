<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nombre' => $this->faker->name,
            'apellidos' => $this->faker->lastName,
            'telefono' => $this->faker->tollFreePhoneNumber,
            'img'=> "https://previews.123rf.com/images/faysalfarhan/faysalfarhan1605/faysalfarhan160503861/57240009-cliente-icono-de-miembro-bot%C3%B3n-redondo-de-color-amarillo-brillante.jpg",
            'direccion' => $this->faker->address,
            'cp'=> rand(01000, 33635),
            'colonia'=> $this->faker->randomElement(['San Angel','Florida','Altavista','Loreto','Polvora','El Capulin','Cristo Rey','Chapultepec','Santa Cecilia Tepetlapa']),
        ];
    }
}
