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

   private function parseBranchClass (int $branchId)
   {
      $this->branchClassesRepository->getClasses($branchId);
   }

  /**
   * Creates and renders sign in form
   * @param int $branchId
   * @param callable $onSuccess
   * @return Form
   */
  public function create(int $branchId, callable $onSuccess): Form
  {
    $form = $this->formFactory->create();
    $classes = $this->branchClassesRepository->getForApplicationForm($branchId);
    $currentYear = date('Y');
    $lastYear =  date('Y',strtotime('-1 year'));

    $form->addText('name', 'Meno a priezvisko')
      ->setHtmlAttribute('placeholder','Arnošt Kábel')
      ->setRequired()
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 255);

    $form->addText('title_bn', 'Titul pred menom')
      ->setHtmlAttribute('placeholder', 'Ing.')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 50);

    $form->addText('title_an', 'Titul za menom')
      ->setHtmlAttribute('placeholder', 'PhD.')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 50);

    $form->addText('street_address', 'Ulica')
      ->setHtmlAttribute('placeholder','Javorová 16')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 255)
      ->setRequired();

    $form->addText('city', 'Mesto')
      ->setHtmlAttribute('placeholder','Spišská Nová Ves')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 255)
      ->setRequired();

    $form->addText('zipcode', 'PSČ')
      ->setHtmlAttribute('placeholder','05201')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 10)
      ->setRequired();

    // TODO: Check date HTML attribute
    $form->addText('birthdate', 'Dátum narodenia')
      ->setHtmlType('date')
      ->setHtmlAttribute('placeholder','2000-10-15')
      ->setRequired();

    $form->addText('birthplace', 'Miesto narodenia')
      ->setHtmlAttribute('placeholder','Spišská Nová Ves')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 255)
      ->setRequired();

    $form->addText('id_number', 'Rodné číslo')
      ->setHtmlAttribute('placeholder', 'ABCDEF/WXYZ')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 15)
      ->setRequired();

    $form->addText('nationality', 'Národnosť')
      ->setHtmlAttribute('placeholder', 'slovenská')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 50)
      ->setRequired();

    $form->addText('email', 'E-mail')
      ->setHtmlType('email')
      ->setHtmlAttribute('placeholder', 'js@jssnv.sk')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 50)
      ->setRequired();

    $form->addText('phone', 'Telefón')
      ->setHtmlAttribute('placeholder', '+421 9XX XXX XXX')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 50)
      ->setRequired();

    $form->addText('employment', 'Zamestnanie (žiaci a študenti: škola, ročník)')
      ->setHtmlAttribute('placeholder', 'Gymnázium Javorová, 4. ročník')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 255)
      ->setRequired();

    $form->addText('prev_course', 'Absolvovaný kurz na JŠ v šk. roku ' . $lastYear . '/' . $currentYear)
      ->setHtmlAttribute('placeholder', 'Anglický jazyk, 1. ročník')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 255);

    $form->addMultiSelect('branch_class_id', 'Prihlasujem sa do kurzu', $classes)
      ->setHtmlAttribute('class', 'custom-select')
      ->setRequired();

    $form->addCheckbox('consent_personal_data', 'Spracovanie osobných údajov')
      ->setRequired();

    $form->addCheckbox('consent_name', 'Zverejnenie v zozname poslucháčov');

    $form->addCheckbox('consent_photo', 'Zverejnenie fotografií');

    $form->addSubmit('submit', 'Odoslať');

    FormHelper::setBootstrapFormRenderer($form);

    $form->onSuccess[] = function (Form $form, array $values) use ($onSuccess) {
      $onSuccess($form, $values);
    };

    return $form;
  }
}