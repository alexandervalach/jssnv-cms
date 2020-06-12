<?php

declare(strict_types=1);

namespace App\Forms;

use App\Helpers\FormHelper;
use App\Model\LevelsRepository;
use Nette\SmartObject;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Add upload form factory
 * @package App\Forms
 */
class QuestionFormFactory
{
  use SmartObject;

  /** @var FormFactory */
  private $formFactory;

  /**
   * @var LevelsRepository
   */
  private $levelsRepository;

  /**
   * @param FormFactory $factory
   * @param LevelsRepository $levelsRepository
   */
  public function __construct(FormFactory $factory, LevelsRepository $levelsRepository)
  {
    $this->formFactory = $factory;
    $this->levelsRepository = $levelsRepository;
  }

  /**
   * Creates and renders sign in form
   * @param callable $onSuccess
   * @return Form
   */
  public function create(callable $onSuccess): Form
  {
    $form = $this->formFactory->create();
    $levels = $this->levelsRepository->getLevels();

    $form->addText('label', 'Znenie otázky')
        ->setRequired()
        ->addRule(Form::MAX_LENGTH, '%label môže mať maximálne %value znakov.', 255);

    $form->addSelect('level_id', 'Úroveň*', $levels)
        ->setRequired();

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