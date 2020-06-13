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
}
