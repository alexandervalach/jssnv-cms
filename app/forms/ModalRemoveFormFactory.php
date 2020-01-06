<?php

declare(strict_types = 1);

namespace App\Forms;

use App\Helpers\FormHelper;
use Nette\SmartObject;
use Nette\Application\UI\Form;

/**
 * @package App\Forms
 */
class ModalRemoveFormFactory
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
   * Creates and renders remove form
   * @param callable $onSuccess
   * @return Form
   */
  public function create(callable $onSuccess): Form
  {
    $form = $this->formFactory->create();
    $form->addSubmit('remove', 'Odstrániť')
        ->setHtmlAttribute('class', 'btn btn-large btn-danger');
    $form->addSubmit('cancel', 'Zrušiť')
        ->setHtmlAttribute('class', 'btn btn-large btn-warning')
        ->setHtmlAttribute('data-dismiss', 'modal');
    $form->addProtection('Vypršal bezpečnostný limit. Odošlite, prosím, formulár znova.');
    FormHelper::setBootstrapFormRenderer($form);

    $form->onSuccess[] = function () use ($onSuccess) {
      $onSuccess();
    };

    return $form;
  }
}