<?php

declare(strict_types=1);

namespace App\Helpers;

use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;
use Nette\Forms\Controls\SelectBox;
use Nette\Forms\Controls\SubmitButton;
use Nette\Forms\Controls\TextArea;
use Nette\Forms\Controls\TextInput;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;

/**
 * Class FormHelper
 * @package App
 */
class ApplicationHelper {

  /**
   * @param array $appForms
   * @return ArrayHash
   */
  public static function setAppFormsStyle (array $appForms): ArrayHash
  {
    foreach ($appForms as $appForm) {
      if ((string) $appForm['status'] === (string) "pending") {
        $appForm['class'] = 'table-warning';
        $appForm['status_label'] = 'Čakajúca';
      } elseif ((string) $appForm['status'] === (string) "finished") {
        $appForm['status_label'] = 'Vybavená';
        $appForm['class'] = 'table-success';
      } elseif ((string) $appForm['status'] === (string) "cancelled") {
        $appForm['status_label'] = 'Zrušená';
        $appForm['class'] = 'table-danger';
      } else {
        $appForm['status_label'] = 'Archivovaná';
        $appForm['class'] = 'table-default';
      }
    }
    return ArrayHash::from($appForms);
  }

  public static function setAppFormStyle ($appForm)
  {
    if ((string) $appForm->status === (string) "pending") {
      $appForm->class = 'bg-warning text-dark';
      $appForm->status_label = 'Čakajúca';
    } elseif ((string) $appForm->status === (string) "finished") {
      $appForm->status_label = 'Vybavená';
      $appForm->class = 'bg-primary text-white';
    } elseif ((string) $appForm->status === (string) "cancelled") {
      $appForm->status_label = 'Zrušená';
      $appForm->class = 'bg-danger text-white';
    } else {
      $appForm->status_label = 'Archivovaná';
      $appForm->class = 'bg-default text-dark';
    }
    return $appForm;
  }

  public static function parseName ($name, $titleBn = null, $titleAn = null) {
    $finalName = $name;

    if ($titleBn) {
      $finalName = $titleBn . ' ' . $finalName;
    }

    if ($titleAn) {
      $finalName .= ', ' . $titleAn;
    }

    return $finalName;
  }

  public static function parseFullName (string $fullname) {
    $names = explode(' ', $fullname);
    return [
      'fname' => $names[0],
      'lname' => $names[1]
    ];
  }

  public static function parseTitles (string $titleBn = null, string $titleAn = null) {
    if (!$titleBn && !$titleAn) {
      return '-';
    }

    $title = '';

    if ($titleBn) {
      $title .= $titleBn;
    }

    if ($titleAn) {
      $title .= ', ' . $titleAn;
    }

    return $title;
  }

  public static function getSchoolYear (string $delimiter = '/') {
    return date('Y') . $delimiter . date('Y', strtotime('+1 year'));
  }

  public static function parseAddress (string $streetAddress, string $city) {
    return $streetAddress . ', ' . $city;
  }

  public static function getPrevSchoolYear (string $delimiter = '/') {
    return date('Y', strtotime('-1 year')) . $delimiter . date('Y');
  }

  public static function parseSignDate (string $date) {
    return date('d.m.Y', strtotime($date));
  }
}
