<?php

declare(strict_types=1);

namespace App\Forms;

use App\Helpers\FormHelper;
use Nette\SmartObject;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Add upload form factory
 * @package App\Forms
 */
class PasswordFormFactory
{
  use SmartObject;

  /** @var FormFactory */
  private $formFactory;

  /**
   * @param FormFactory $factory
   */
  public function __construct(FormFactory $factory)
  {
    $this->formFactory = $factory;
  }

  /**
   * Creates and renders sign in form
   * @param callable $onSuccess
   * @return Form
   */
  public function create(callable $onSuccess): Form
  {
    $form = $this->formFactory->create();

    $form->addPassword('password', 'Heslo*')
        ->setHtmlAttribute('autocomplete', 'new-password')
        ->addRule(Form::FILLED, 'Heslo musí byť vyplnené.')
        ->addRule(Form::MAX_LENGTH, 'Heslo môže mať maximálne 30 znakov.', 30)
        ->addRule(Form::MIN_LENGTH, 'Heslo musí mať minimálne 5 znakov.', 5);
    $form->addPassword('password_again', 'Heslo znovu*')
        ->setHtmlAttribute('autocomplete', 'new-password')
        ->addRule(Form::FILLED, 'Heslo znovu musí byť vyplnené.')
        ->addRule(Form::EQUAL, 'Heslá sa nezhodujú.', $form['password']);
    $form->addSubmit('save', 'Uložiť')
        ->setHtmlAttribute('class', 'btn btn-primary');
    $form->addSubmit('cancel', 'Zrušiť')
        ->setHtmlAttribute('class', 'btn btn-warning')
        ->setHtmlAttribute('data-dismiss', 'modal');
    $form->addProtection('Vypršal časový limit, odošli formulár znovu.');
    FormHelper::setBootstrapFormRenderer($form);

    $form->onSuccess[] = function (Form $form, ArrayHash $values) use ($onSuccess) {
      $onSuccess($form, $values);
    };

    return $form;
  }
}