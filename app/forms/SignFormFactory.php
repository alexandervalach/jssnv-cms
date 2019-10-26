<?php

namespace App\Forms;

use App\FormHelper;
use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;

/**
 * Class SignFormFactory
 * @package App\Forms
 */
class SignFormFactory {

  /** @var User */
  private $user;

  /**
   * SignFormFactory constructor.
   * @param User $user
   */
  public function __construct(User $user) {
    $this->user = $user;
  }

  /**
   * @return Form
   */
  public function create() {
    $form = new Form;
    $form->addText('username', 'Používateľské meno')
        ->setRequired('Ešte chýba používateľské meno.');

    $form->addPassword('password', 'Heslo')
        ->setRequired('Ešte chýba heslo.');

    $form->addCheckbox('remember', ' Zapamätať si ma na 14 dní');
    $form->addSubmit('send', 'Prihlásiť');

    $form->onSuccess[] = [$this, 'formSucceeded'];
    FormHelper::setBootstrapRenderer($form);
    return $form;
  }

  /**
   * @param $form
   * @param $values
   */
  public function formSucceeded($form, $values) {
    $values->remember ? $this->user->setExpiration('14 days') : $this->user->setExpiration('30 minutes');
    try {
      $this->user->login($values->username, $values->password);
    } catch (Nette\Security\AuthenticationException $e) {
      $form->addError($e->getMessage());
    }
  }

}
