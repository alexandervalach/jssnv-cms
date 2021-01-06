<?php

declare(strict_types=1);

namespace App\Forms;

use App\Helpers\FormHelper;
use App\Helpers\ImageHelper;
use Nette\SmartObject;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Add upload form factory
 * @package App\Forms
 */
class SlideFormFactory
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
        ->setRequired()
        ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov.', 255);

    $form->addUpload('image', 'Obrázok')
        ->setRequired()
        ->addRule(Form::MIME_TYPE, '%label môže byť len vo formáte PNG, JPG, GIF, SVG', ImageHelper::IMAGE_MIME_TYPES)
        ->addRule(Form::MAX_FILE_SIZE,  '%label môže mať veľkosť len do 8 MiB', 8 * 1024 * 1024);

    $form->addTextArea('message', 'Text')
        ->setHtmlAttribute('id', 'text-editor');

    $form->addText('link', 'Odkaz')
        ->addRule(Form::MAX_LENGTH, '%label môže mať najviac %value znakov', 255);

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