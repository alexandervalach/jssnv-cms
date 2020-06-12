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
class EditSlideFormFactory
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

    $form->addText('title', 'Názov')
        ->setHtmlAttribute('placeholder','Anglický jazyk')
        ->setRequired()
        ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov.', 255);

    $form->addTextArea('message', 'Text')
        ->setHtmlAttribute('placeholder', 'Pravidelné kurzy anglického jazyka')
        ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 1000);

    $form->addText('link', 'Odkaz')
        ->setHtmlAttribute('placeholder', 'https://jssnv.sk')
        ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 255);

    $form->addSubmit('save', 'Uložiť');

    $form->addSubmit('cancel', 'Zrušiť')
        ->setHtmlAttribute('class', 'btn btn-warning')
        ->setHtmlAttribute('data-dismiss', 'modal');
    
    FormHelper::setBootstrapFormRenderer($form);

    $form->onSuccess[] = function (Form $form, ArrayHash $values) use ($onSuccess) {
      $onSuccess($form, $values);
    };

    return $form;
  }
}