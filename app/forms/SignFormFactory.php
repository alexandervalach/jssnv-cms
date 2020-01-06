<?php

declare(strict_types=1);

namespace App\Forms;

use App\Helpers\FormHelper;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Security\User;
use Nette\SmartObject;
use Nette\Utils\ArrayHash;

/**
 * Class SignFormFactory
 * @package App\Forms
 */
class SignFormFactory {

  use SmartObject;

  /** @var FormFactory */
  private $formFactory;

  /** @var User */
  private $user;

  /**
   * SignFormFactory constructor.
   * @param FormFactory $formFactory
   */
  public function __construct(FormFactory $formFactory) {
    $this->formFactory = $formFactory;
  }

  /**
   * @param callable $onSuccess
   * @return Form
   */
  public function create(callable $onSuccess): Form
  {
    $form = new Form;
    $form->addText('username', 'Používateľské meno*')
        ->setRequired();

    $form->addPassword('password', 'Heslo*')
        ->setRequired();

    $form->addCheckbox('remember', ' Zapamätať si ma na 14 dní');
    $form->addSubmit('send', 'Prihlásiť');

    FormHelper::setBootstrapFormRenderer($form);

    $form->onSuccess[] = function (Form $form, ArrayHash $values) use ($onSuccess) {
      $onSuccess($form, $values);
    };

    return $form;
  }

}
