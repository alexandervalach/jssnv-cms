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
class ImageUploadFormFactory
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
    $form->addUpload('image', 'Obrázok*')
        ->setRequired()
        ->addRule(Form::MIME_TYPE, 'Obrázok môže byť len vo formáte PNG, JPG, GIF, SVG', ImageHelper::IMAGE_MIME_TYPES)
        ->addRule(Form::MAX_FILE_SIZE, 'Obrázok môže mať veľkosť maximálne 2 MiB', 2 * 1024 * 1024);
    $form->addSubmit('upload', 'Nahrať');
    $form->addSubmit('cancel', 'Zrušiť')
        ->setHtmlAttribute('class', 'btn btn-large btn-warning')
        ->setHtmlAttribute('data-dismiss', 'modal');
    FormHelper::setBootstrapFormRenderer($form);

    $form->onSuccess[] = function (Form $form, ArrayHash $values) use ($onSuccess) {
      $onSuccess($form, $values);
    };

    return $form;
  }
}