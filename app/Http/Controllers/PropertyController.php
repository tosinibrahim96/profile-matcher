<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\SearchProfile;
use Illuminate\Http\Request;



class PropertyController extends Controller
{

  /**
   * __construct
   *
   * @return void
   */
  public function __construct()
  {
  }



  /**
   * Get profiles that matched a given property 
   * 
   * @param  \Illuminate\Http\Request $request
   * @param  mixed $propertyId
   * @return \Illuminate\Http\JsonResponse
   */
  public function getMatchedProfiles(Request $request, $propertyId)
  {
    $property = Property::find($propertyId);
    $recordsPerPage = $request->per_page ?? 10;

    if (is_null($property)) {

      $response = [
        'status' => false,
        'message' => 'Property not found',
        'data' => []
      ];

      return response()->json($response, 404);
    }

    $propertyFields = json_decode($property->fields);

    $results = SearchProfile::getMatchedProfilesForProperty($property)
      ->where('property_type_id', $property->property_type_id)
      ->paginate($recordsPerPage);

    
    $results->setCollection(
      buildMatchedProfilesResponseData(
        $results->getCollection(),
        $propertyFields
      )
    );
    
    $response = [
      'status' => true,
      'message' => 'Search profiles retrieved successfully',
      'data' => $results
    ];

    return response()->json($response, 200);
  }
}
