<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TipoEquipo>
 */
class TipoEquipoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'tipo' => $this->faker->randomElement(['Laptop', 'Smartphone', 'Tablet','PC','Otros']),
            'estado' => $this->faker->boolean,
        ];
    }
}
