<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyType extends Model
{
  use HasFactory;

      /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

  /**
   * Get the properties for the property type.
   */
  public function properties()
  {
    return $this->hasMany(Property::class);
  }

  /**
   * Get the search profiles for the property type.
   */
  public function searchProfiles()
  {
    return $this->hasMany(SearchProfile::class);
  }
}
