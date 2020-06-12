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
class SectionFormFactory
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
    $sections = $this->sectionsRepository->fetchAll();
    $form = $this->formFactory->create();

    $form->addText('name', 'Názov')
        ->setRequired()
        ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov.', 255);

    $form->addSelect('section_id', 'Sekcie', $sections);

    $form->addText('order', 'Poradie')
        ->setRequired()
        ->setHtmlType('number')
        ->setDefaultValue(50)
        ->addRule(Form::INTEGER, 'Poradie môže byť len celé číslo.');

    $form->addSubmit('save', 'Uložiť');

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