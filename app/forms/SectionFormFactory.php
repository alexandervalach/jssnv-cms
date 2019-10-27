<?php

declare(strict_types = 1);

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

    $form->addText('name', 'Názov*')
        ->setRequired()
        ->addRule(Form::MAX_LENGTH, 'Názov môže mať maximálne 50 znakov.', 50);

    $form->addSelect('section_id', 'Sekcie', $sections);

    $form->addText('url', 'URL adresa');

    $form->addCheckbox('home_url', ' URL na tejto stránke')
        ->setDefaultValue(0);

    $form->addText('order', 'Poradie*')
        ->setRequired()
        ->setDefaultValue(50)
        ->addRule(Form::INTEGER, 'Poradie môže byť len celé číslo.');

    $form->addCheckbox('visible', ' Viditeľné v bočnom menu')
        ->setDefaultValue(1);

    $form->addCheckbox('sliding', ' Rolovacie menu');

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