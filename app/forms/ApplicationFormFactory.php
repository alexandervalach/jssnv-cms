<?php

declare(strict_types=1);

namespace App\Forms;

use App\Helpers\FormHelper;
use App\Model\BranchesClassesRepository;
use Nette\Application\UI\Form;
use Nette\SmartObject;

class ApplicationFormFactory
{
  use SmartObject;

  /** @var FormFactory */
  private $formFactory;

  /** @var BranchesClassesRepository */
  private $branchClassesRepository;

  /**
   * @param FormFactory $factory
   * @param BranchesClassesRepository $branchClassesRepository
   */
  public function __construct(FormFactory $factory, BranchesClassesRepository $branchClassesRepository)
  {
    $this->formFactory = $factory;
    $this->branchClassesRepository = $branchClassesRepository;
  }

  /**
   * Creates and renders sign in form
   * @param callable $onSuccess
   * @return Form
   */
  public function create(callable $onSuccess): Form
  {
    $form = $this->formFactory->create();
    $currentYear = date('Y');
    $lastYear =  date('Y', strtotime('-1 year'));

    $form->addText('name', 'Meno a priezvisko')
      ->setRequired()
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 255);

    $form->addText('title_bn', 'Titul pred menom')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 50);

    $form->addText('title_an', 'Titul za menom')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 50);

    $form->addText('street_address', 'Ulica')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 255)
      ->setRequired();

    $form->addText('city', 'Mesto')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 255)
      ->setRequired();

    $form->addText('zipcode', 'PSČ')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 10)
      ->setRequired();

    // TODO: Check date HTML attribute
    $form->addText('birthdate', 'Dátum narodenia')
      ->setHtmlType('date')
      ->setRequired();

    $form->addText('birthplace', 'Miesto narodenia')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 255)
      ->setRequired();

    $form->addText('id_number', 'Rodné číslo')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 15)
      ->setRequired();

    $form->addText('nationality', 'Národnosť')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 50)
      ->setRequired();

    $form->addText('email', 'E-mail')
      ->setHtmlType('email')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 50)
      ->setRequired();

    $form->addText('phone', 'Telefón')
      ->setHtmlAttribute('placeholder', '+421 9XX XXX XXX')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 50)
      ->setRequired();

    $form->addText('employment', 'Zamestnanie (žiaci a študenti: škola, ročník)')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 255)
      ->setRequired();

    $form->addText('prev_course', 'Absolvovaný kurz na JŠ v šk. roku ' . $lastYear . '/' . $currentYear)
      ->setHtmlAttribute('placeholder', 'Anglický jazyk, 1. ročník')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 255);

    /*
    $form->addCheckboxList('branch_class_id', 'Prihlasujem sa do kurzu', $classes)
      // ->setHtmlAttribute('class', 'custom-select')
      ->setRequired();
    */

    /*
    $form->addCheckbox('consent_personal_data', 'Spracovanie osobných údajov')
      ->setRequired();

    $form->addCheckbox('consent_name', 'Zverejnenie v zozname poslucháčov');

    $form->addCheckbox('consent_photo', 'Zverejnenie fotografií');
    */

    $form->addTextArea('note', 'Poznámka')
      ->addRule(Form::MAX_LENGTH, '%label môže mať najviac %value znakov', 1000);

    $form->addSubmit('submit', 'Odoslať prihlášku');

    FormHelper::setBootstrapFormRenderer($form);

    $form->onSuccess[] = function (Form $form, array $values) use ($onSuccess) {
      $onSuccess($form, $values);
    };

    return $form;
  }
}