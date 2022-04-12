<?php


if (!function_exists('buildMatchedProfilesResponseData')) {

  /**
   * Build the response structure from the matched profiles
   * for a property
   *
   * @param \Illuminate\Database\Eloquent\Collection $matchedProfilesForProperty
   * @return array
   */
  function buildMatchedProfilesResponseData($matchedProfilesForProperty, $fieldsToCheck)
  {
    $profiles = [];

    foreach ($matchedProfilesForProperty as $key => $data) {

      $profileFields = json_decode($data->fields);
      $strictMatchesCount = $looseMatchesCount = $score = 0;

      $profileInfo = [
        'searchProfileId' => $data->id,
        'strictMatchesCount' => $strictMatchesCount,
        'looseMatchesCount' => $looseMatchesCount,
        'score' => $score
      ];

      foreach ($fieldsToCheck as $field => $fieldValue) {

        if (is_numeric($fieldValue)) {

          if (!isset($profileFields->{$field})) {
            continue;
          }

          if (is_null($profileFields->{$field}[0]) || is_null($profileFields->{$field}[1])) {

            $field == 'yearOfConstruction' ?
              setMatchesCountForDateRange($profileInfo, $profileFields->{$field}[0], $profileFields->{$field}[1], $fieldValue) :
              setMatchesCountForNullRange($profileInfo, $profileFields->{$field}[0], $profileFields->{$field}[1], $fieldValue);
          } else {
            $field == 'yearOfConstruction' ?
              setMatchesCountForDateRange($profileInfo, $profileFields->{$field}[0], $profileFields->{$field}[1], $fieldValue) :
              setMatchesCountForGenericValuesRange($profileInfo, $profileFields->{$field}[0], $profileFields->{$field}[1], $fieldValue);
          }
        } else {

          if (!isset($profileFields->{$field})) {
            continue;
          }

          $strictMatchBonus = 1000;

          if ($profileFields->{$field} == $fieldValue) {
            $profileInfo['strictMatchesCount'] += $strictMatchBonus;
            $profileInfo['score'] += $strictMatchBonus;
          }
        }
      }

      $profiles[] = $profileInfo;
    }

    return collect($profiles)->sortByDesc('score')->values();
  }
}


if (!function_exists('setMatchesCountForNullRange')) {

  /**
   * Set the values for strict match loose match and score
   * if there's a null value in the range of values for a 
   * field
   *
   * @param array $profileInfo
   * @param mixed $lowerBound
   * @param mixed $upperBound
   * @param mixed $fieldValue
   * @return array
   */
  function setMatchesCountForNullRange(&$profileInfo, $lowerBound, $upperBound, $fieldValue)
  {
    $strictMatchBonus = 1000;
    $looseMatchesBonus = 500;
    $lowerBoundDeviation = $upperBoundDeviation = 0;

    if (is_null($lowerBound)) {

      if ($fieldValue <= $upperBound) {

        $profileInfo['strictMatchesCount'] += $strictMatchBonus;
        $profileInfo['score'] += $strictMatchBonus;
      } else {
        $upperBoundDeviation = $upperBound * 1.25;

        if ($fieldValue <= $upperBoundDeviation) {
          $profileInfo['looseMatchesCount'] += $looseMatchesBonus;
          $profileInfo['score'] += $looseMatchesBonus;
        }
      }
    }

    if (is_null($upperBound)) {

      if ($fieldValue >= $lowerBound) {

        $profileInfo['strictMatchesCount'] += $strictMatchBonus;
        $profileInfo['score'] += $strictMatchBonus;
      } else {
        $lowerBoundDeviation = $lowerBound - (0.25 * $lowerBound);

        if ($fieldValue >= $lowerBoundDeviation) {
          $profileInfo['looseMatchesCount'] += $looseMatchesBonus;
          $profileInfo['score'] += $looseMatchesBonus;
        }
      }
    }
  }
}


if (!function_exists('setMatchesCountForGenericValuesRange')) {

  /**
   * Set the values for strict match loose match and score
   * if there's no null value in the range of values for a 
   * field
   *
   * @param array $profileInfo
   * @param mixed $lowerBound
   * @param mixed $upperBound
   * @param mixed $fieldValue
   * @return array
   */
  function setMatchesCountForGenericValuesRange(&$profileInfo, $lowerBound, $upperBound, $fieldValue)
  {
    $strictMatchBonus = 1000;
    $looseMatchesBonus = 500;
    $lowerBoundDeviation = $upperBoundDeviation = 0;

    if ($lowerBound <= $fieldValue  && $upperBound >= $fieldValue) {

      $profileInfo['strictMatchesCount'] += $strictMatchBonus;
      $profileInfo['score'] += $strictMatchBonus;
    } else {

      $lowerBoundDeviation = $lowerBound - (0.25 * $lowerBound);
      $upperBoundDeviation = $upperBound * 1.25;

      if ($lowerBoundDeviation <= $fieldValue  && $upperBoundDeviation >= $fieldValue) {
        $profileInfo['looseMatchesCount'] += $looseMatchesBonus;
        $profileInfo['score'] += $looseMatchesBonus;
      }
    }
  }
}

if (!function_exists('setMatchesCountForDateRange')) {

  /**
   * Set the values for strict match loose match and score
   * for a date field
   *
   * @param array $profileInfo
   * @param mixed $lowerBound
   * @param mixed $upperBound
   * @param mixed $fieldValue
   * @return array
   */
  function setMatchesCountForDateRange(&$profileInfo, $lowerBound, $upperBound, $fieldValue)
  {
    $strictMatchBonus = 1000;
    $looseMatchesBonus = 500;
    $lowerBoundDeviation = $upperBoundDeviation = 0;

    if (is_null($lowerBound)) {

      if ($fieldValue <= $upperBound) {

        $profileInfo['strictMatchesCount'] += $strictMatchBonus;
        $profileInfo['score'] += $strictMatchBonus;
      } else {
        $upperBoundDeviation = $upperBound + 4;

        if ($fieldValue <= $upperBoundDeviation) {
          $profileInfo['looseMatchesCount'] += $looseMatchesBonus;
          $profileInfo['score'] += $looseMatchesBonus;
        }
      }

      return;
    }

    if (is_null($upperBound)) {
      if ($fieldValue >= $lowerBound) {

        $profileInfo['strictMatchesCount'] += $strictMatchBonus;
        $profileInfo['score'] += $strictMatchBonus;
      } else {
        $lowerBoundDeviation = $lowerBound - 4;

        if ($fieldValue >= $lowerBoundDeviation) {
          $profileInfo['looseMatchesCount'] += $looseMatchesBonus;
          $profileInfo['score'] += $looseMatchesBonus;
        }
      }

      return;
    }

    if ($lowerBound <= $fieldValue  && $upperBound >= $fieldValue) {

      $profileInfo['strictMatchesCount'] += $strictMatchBonus;
      $profileInfo['score'] += $strictMatchBonus;
    } else {

      $lowerBoundDeviation = $lowerBound - 4;
      $upperBoundDeviation = $upperBound + 4;

      if ($lowerBoundDeviation <= $fieldValue  && $upperBoundDeviation >= $fieldValue) {
        $profileInfo['looseMatchesCount'] += $looseMatchesBonus;
        $profileInfo['score'] += $looseMatchesBonus;
      }
    }
  }
}
