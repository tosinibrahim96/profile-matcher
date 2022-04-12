<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class SearchProfileFactory extends Factory
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
      'fields' => json_encode([
        "area" => $this->generateRangeArray(1, 600),
        "yearOfConstruction" => $this->generateRangeArray(1990, 2022),
        "rooms" => $this->generateRangeArray(1, 20),
        "heatingType" => $this->faker->randomElement(['gas', 'electric', null]),
        "parking" => $this->faker->randomElement([true, false]),
        "returnActual" => $this->generateRangeArray(1, 100),
        "price" => $this->generateRangeArray(1000, 10000000),
      ]),
      'property_type_id' => '',
    ];
  }


  /**
   * generateRangeArray
   *
   * @param  mixed $min
   * @param  mixed $max
   * @return array
   */
  private function generateRangeArray($min, $max)
  {
    $values = [];
    for ($i = 1; $i <= 2; $i++) {

      $values[] = $this->faker->numberBetween($min, $max);
    }

    sort($values);

    return $values;
  }
}
