<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\PropertyType;
use App\Models\SearchProfile;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    // \App\Models\User::factory(10)->create();
    PropertyType::factory(10)
      ->has(Property::factory()->count(3))
      ->has(SearchProfile::factory()->count(3))
      ->create();
  }
}
