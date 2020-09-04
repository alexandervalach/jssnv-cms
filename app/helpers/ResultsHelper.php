<?php

declare(strict_types=1);

namespace App\Helpers;


class ResultsHelper {

  public static function getRecommendedLevel ($score, $course) {
    if ($course === 'Anglický jazyk' || $course === 'Nemecký jazyk') {
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

    if ($course === 'Taliansky jazyk') {
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

    if ($course === 'Španielsky jazyk') {
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
}