<?php

namespace Database\Factories;

use App\Models\PropertyType;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'name' => $this->faker->text(),
      'address' => $this->faker->address(),
      'fields' => json_encode([
        "area" => $this->faker->numberBetween(1, 500),
        "yearOfConstruction" => $this->faker->numberBetween(1990, 2022),
        "rooms" => $this->faker->numberBetween(1, 20),
        "heatingType" => $this->faker->randomElement(['gas', 'electric']),
        "parking" => $this->faker->randomElement([true, false]),
        "returnActual" => $this->faker->randomFloat(1, 1, 100),
        "price" => $this->faker->numberBetween(1000, 10000000),
      ]),
      'property_type_id' => '',
    ];
  }
}
