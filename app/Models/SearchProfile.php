<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class SearchProfile extends Model
{
  use HasFactory;


  /**
   * Scope a query to get all matched profiles for
   * a property
   *
   * @param  \Illuminate\Database\Eloquent\Builder  $query
   * @param  \App\Models\Property  $property
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeGetMatchedProfilesForProperty($query, $property)
  {
    $propertyFields = json_decode($property->fields);

    return $query->where(function ($q) use ($propertyFields) {

      foreach ($propertyFields as $key => $value) {

        if (is_numeric($value)) {

          $lowerBound = "$.{$key}[0]";
          $upperBound = "$.{$key}[1]";

          $q->orWhere(
            /**
             * Where there's a strict match (range values)
             * 
             * i.e
             * 
             * Value falls between upperBound and lowerBound
             */
            function ($q) use ($key, $value, $lowerBound, $upperBound) {

              return $q
                ->whereRaw(
                  'json_extract(fields,' . '"' . $lowerBound . '"' . ') <= ?',
                  [$value]
                )
                ->whereRaw(
                  'json_extract(fields,' . '"' . $upperBound . '"' . ')  >= ?',
                  [$value]
                );
            }

          )->orWhere(
            function ($q) use ($key, $value, $lowerBound, $upperBound) {

              /**
               * Where there's a loose match of 25% deviation
               * 
               * i.e
               * decrease the lowerBound by 25% = (value - (25/100 * value))
               * increase the upperBound by 25% = (value * (1+25/100))
               * 
               * If our value still falls between the derived lowe and upper bounds
               *  after both calculations, then we have a loose matched value
               */
              return $q
                ->whereRaw(
                  'json_extract(fields,' . '"' . $lowerBound . '"' . ') - (0.25 *' . 'json_extract(fields,' . '"' . $lowerBound . '"' . ')' . ') <= ?',
                  [$value]
                )
                ->whereRaw(
                  'json_extract(fields,' . '"' . $upperBound . '"' . ') * 1.25 >= ?',
                  [$value]
                );
            }
          )->orWhere(
            /**
             * Where the lower bound value is null
             */
            function ($q) use ($lowerBound) {

              return $q->whereRaw('json_extract(fields,' . '"' . $lowerBound . '"' . ') IS NULL');
            }

          )->orWhere(
            /**
             * Where the upper bound value is null
             */
            function ($q) use ($upperBound) {

              return $q->whereRaw('json_extract(fields,' . '"' . $upperBound . '"' . ') IS NULL');
            }

          );
        } else {

          /**
           * Where there's a strict match (direct values)
           * 
           * i.e
           * 
           * Value is exactly the same in search profile
           */

          $key = "$.{$key}";

          $q->orWhere(
            function ($q) use ($key, $value) {
              return $q->whereRaw(
                'json_extract(fields,' . '"' . $key . '"' . ') = ?',
                [$value]
              );
            }
          );
        }
      }
    });
  }
}
