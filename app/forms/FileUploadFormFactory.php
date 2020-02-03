<?php

declare(strict_types=1);

namespace App\Forms;

use App\Helpers\FileHelper;
use App\Helpers\FormHelper;
use Nette\SmartObject;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Add upload form factory
 * @package App\Forms
 */
class FileUploadFormFactory
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
    $form->addMultiUpload('files', 'Súbory')
        ->setRequired()
        ->addRule(Form::MAX_FILE_SIZE,  'Súbor môže maž maximálne veľkosť 2 MiB', 2 * 1024 * 1024)
        ->addRule(Form::MIME_TYPE, 'Súbor môže byť len dokument, archív alebo MP3', FileHelper::FILE_MIME_TYPES);
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