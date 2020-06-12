<?php

declare(strict_types=1);

namespace App\Forms;

use App\Helpers\FormHelper;
use App\Model\SectionsRepository;
use Nette\SmartObject;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Add upload form factory
 * @package App\Forms
 */
class PostFormFactory
{
  use SmartObject;

  /** @var FormFactory */
  private $formFactory;

  /** @var SectionsRepository */
  private $sectionsRepository;

  /**
   * @param FormFactory $factory
   * @param SectionsRepository $sectionsRepository
   */
  public function __construct(FormFactory $factory, SectionsRepository $sectionsRepository)
  {
    $this->formFactory = $factory;
    $this->sectionsRepository = $sectionsRepository;
  }

  /**
   * Creates and renders sign in form
   * @param callable $onSuccess
   * @return Form
   */
  public function create(callable $onSuccess): Form
  {
    $form = $this->formFactory->create();

    $form->addText('name', 'Názov')
        ->setRequired();
    $form->addTextArea('content', 'Obsah')
        ->setHtmlAttribute('id', 'ckeditor');
    $form->addCheckbox('onHomepage', ' Na domovskej stránke');
    $form->addSubmit('save', 'Uložiť');
    $form->addSubmit('cancel', 'Zrušiť')
        ->setHtmlAttribute('data-dismiss', 'modal')
        ->setHtmlAttribute('class', 'btn btn-large btn-warning');
    FormHelper::setBootstrapFormRenderer($form);

    $form->onSuccess[] = function (Form $form, ArrayHash $values) use ($onSuccess) {
      $onSuccess($form, $values);
    };

    return $form;
  }
}