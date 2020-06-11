<?php

declare(strict_types=1);

namespace App\Forms;

use App\Helpers\FormHelper;
use App\Model\BranchClassesRepository;
use Nette\Application\UI\Form;
use Nette\SmartObject;
use Nette\Utils\ArrayHash;

class ApplicationFormFactory
{
  use SmartObject;

  const CONSENT_NAME = 'Súhlasím so zverejnením svojho mena, priezviska a akademického titulu
   na zozname poslucháčov prevádzkovateľa, ktorý bude zverejnený v septembri 2019
   na www stránke školy za účelom informovania o prvom stretnutí a čase konania kurzov.';

  const CONSENT_PHOTO = 'Súhlasím so zverejňovaním svojich fotografií/
    fotografií svojej dcéry/syna/videoprodukcie na účely dokumentujúce a 
    propagujúce činnosť prevádzkovateľa, zverejnenia týchto fotografií 
    na www stránke prevádzkovateľa, nástenke prevádzkovateľa a 
    v publikáciách vydávaných prevádzkovateľom. 
    Doba trvania súhlasu platí, pokiaľ trvá účel ich spracovania. 
    Dovtedy ho možno kedykoľvek písomne alebo elektronicky odvolať.';

  const CONSENT_PERSONAL_DATA = 'Súhlas so spracovaním osobných údajov pre Jazykovú školu, 
    Javorová 16, Spišská Nová Ves,
    IČO: 35538791 (ďalej len „prevádzkovateľ“)
    Súhlas so spracovaním osobných údajov v zmysle čl. 6 ods. 1 písm. 
    a) Nariadenia EP a Rady EÚ č. 2016/679 o ochrane fyzických osôb 
    pri spracúvaní osobných údajov a o voľnom pohybe takýchto údajov, 
    ktorým sa zrušuje Smernica 95/46/ES 
    (Všeobecné nariadenie o ochrane údajov, ďalej len „Nariadenie GDPR“).';

  /** @var FormFactory */
  private $formFactory;

  /** @var BranchClassesRepository */
  private $branchClassesRepository;

  /**
   * @param FormFactory $factory
   * @param BranchClassesRepository $branchClassesRepository
   */
  public function __construct(FormFactory $factory, BranchClassesRepository $branchClassesRepository)
  {
    $this->formFactory = $factory;
    $this->branchClassesRepository = $branchClassesRepository;
  }

  /**
   * Creates and renders sign in form
   * @param callable $onSuccess
   * @return Form
   */
  public function create(int $branchId, callable $onSuccess): Form
  {
    $form = $this->formFactory->create();

    $currentYear = date('Y');
    $lastYear =  date('Y',strtotime('-1 year'));

    $form->addText('name', 'Meno a priezvisko')
      ->setHtmlAttribute('placeholder','Arnošt Kábel')
      ->setRequired()
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 255);

    $form->addText('title_bn', 'Titul pred menom')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 50);

    $form->addText('title_an', 'Titul za menom')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 50);

    $form->addText('street_address', 'Adresa trvalého bydliska')
      ->setHtmlAttribute('placeholder','Námestie slobody 1')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 255)
      ->setRequired();

    $form->addText('city', 'Mesto')
      ->setHtmlAttribute('placeholder','Spišská Nová Ves')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 255)
      ->setRequired();

    $form->addText('zipcode', 'PSČ')
      ->setHtmlAttribute('placeholder','Spišská Nová Ves')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 10)
      ->setRequired();

    // TODO: Check date HTML attribute
    $form->addText('birthdate', 'Dátum narodenia')
      ->setHtmlAttribute('type', 'date')
      ->setHtmlAttribute('placeholder','2000-10-15')
      ->setRequired();

    $form->addText('birthplace', 'Miesto narodenia')
      ->setHtmlAttribute('placeholder','Spišská Nová Ves')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 255)
      ->setRequired();

    $form->addText('id_number', 'Rodné číslo')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 15)
      ->setRequired();

    $form->addText('nationality', 'Národnosť')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 50)
      ->setRequired();

    $form->addText('email', 'E-mail')
      ->setHtmlAttribute('type', 'email')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 50)
      ->setRequired();

    $form->addText('phone', 'Telefón')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 50)
      ->setRequired();

    $form->addText('employment', 'Zamestnanie (žiaci a študenti: škola, ročník)')
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 255)
      ->setRequired();

    $form->addText('prev_course', 'Absolvovaný kurz na JŠ v šk. roku ' . $lastYear . '/' . $currentYear)
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 255);

    $form->addCheckbox('consent_personal_data', self::CONSENT_PERSONAL_DATA)
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 255)
      ->setRequired();

    $form->addCheckbox('consent_name', self::CONSENT_NAME)
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 255)
      ->setRequired();

    $form->addCheckbox('consent_photo', self::CONSENT_PHOTO)
      ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov', 255)
      ->setRequired();

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