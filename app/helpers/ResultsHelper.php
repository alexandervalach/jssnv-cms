<?php

declare(strict_types=1);

namespace App\Helpers;


class ResultsHelper {

  private static $levelsAndSkills = [
    'A1 GRAMMAR' => 0,
    'A1 VOCABULARY' => 10,
    'A2 GRAMMAR' => 20,
    'A2 VOCABULARY' => 30,
    'B1 GRAMMAR' => 40,
    'B1 VOCABULARY' => 50,
    'B2 GRAMMAR' => 60,
    'B2 VOCABULARY' => 70,
    'C1 GRAMMAR' => 80,
    'C1 VOCABULARY' => 90
  ];

  private static $levels = [
    'A1' => 0,
    'A2' => 20,
    'B1' => 40,
    'B2' => 60,
    'C1' => 80
  ];

  public static function getRecommendedLevel ($score, $course, $levelsResults) {
    /* This value indicates the lowest level where the user did not meet expectations */
    $lowestRequiredLevel = self::getLowestRequiredLevel($levelsResults);

    if ($course === 'Anglický jazyk') {
      $score = array_key_exists($lowestRequiredLevel['label'], self::$levelsAndSkills) ? $lowestRequiredLevel['score'] : $score;
      return self::evaluateEnglish($score);
    }
    
    if ($course === 'Nemecký jazyk') {
      return $lowestRequiredLevel['label'];
    }

    if ($course === 'Taliansky jazyk') {
      $score = array_key_exists($lowestRequiredLevel['label'], self::$levels) ? $lowestRequiredLevel['score'] : $score;
      return self::evaluateItalian($score);
    }

    if ($course === 'Španielsky jazyk') {
      $score = array_key_exists($lowestRequiredLevel['label'], self::$levels) ? $lowestRequiredLevel['score'] : $score;
      return self::evaluateSpanish($score);
    }
  }

  protected static function getLowestRequiredLevel ($levelsResults) {
    foreach ($levelsResults as $levelResult) {
      if ($levelResult['score'] < 75) {
        return $levelResult;
      }
    }
  }

  protected static function evaluateEnglish ($score) {
    if ($score <= 10) {
      return '0. ročník';
    } else if ($score <= 20) {
      return '1. ročník';
    } else if ($score <= 30) {
      return '2. ročník';
    } else if ($score <= 40) {
      return '3. ročník';
    } else if ($score <= 50) {
      return '4. ročník';
    } else if ($score <= 60) {
      return '5. ročník';
    } else if ($score <= 70) {
      return '6. ročník';
    } else if ($score <= 80) {
      return '7. ročník';
    } else if ($score <= 90) {
      return 'B2';
    } else {
      return 'C1';
    }
  }

  protected static function evaluateItalian ($score) {
    if ($score <= 10) {
      return '0. ročník';
    } else if ($score <= 20) {
      return '1. ročník';
    } else if ($score <= 30) {
      return '2. ročník';
    } else if ($score <= 40) {
      return '3. ročník';
    } else if ($score <= 50) {
      return '4. ročník';
    } else {
      return '5. ročník';
    }
  }

  protected static function evaluateSpanish ($score) {
    if ($score <= 10) {
      return '0. ročník';
    } else if ($score <= 20) {
      return '1. ročník';
    } else if ($score <= 30) {
      return '2. ročník';
    } else {
      return '3. ročník';
    }
  }
}