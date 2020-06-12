<?php

declare(strict_types=1);

namespace App\Forms;

use App\Helpers\FormHelper;
use App\Model\CourseLevelsRepository;
use App\Model\CoursesRepository;
use Nette\SmartObject;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Add upload form factory
 * @package App\Forms
 */
class ClassFormFactory
{
  use SmartObject;

  /** @var FormFactory */
  private $formFactory;

  /**
   * @var CoursesRepository
   */
  private $coursesRepository;

  /**
   * @var CourseLevelsRepository
   */
  private $courseLevelsRepository;

  /**
   * @param FormFactory $factory
   * @param CoursesRepository $coursesRepository
   * @param CourseLevelsRepository $courseLevelsRepository
   */
  public function __construct(FormFactory $factory,
                              CoursesRepository $coursesRepository,
                              CourseLevelsRepository $courseLevelsRepository)
  {
    $this->formFactory = $factory;
    $this->coursesRepository = $coursesRepository;
    $this->courseLevelsRepository = $courseLevelsRepository;
  }

  /**
   * Creates and renders sign in form
   * @param callable $onSuccess
   * @return Form
   */
  public function create(callable $onSuccess): Form
  {
    $form = $this->formFactory->create();

    $courses = $this->coursesRepository->getAll();
    $courseLevels = $this->courseLevelsRepository->getAll();

    $form->addSelect('course_id', 'Kurzy', $courses)
        ->setPrompt('Vyberte kurz')
        ->setRequired();

    $form->addSelect('course_level_id', 'Kurzy', $courseLevels)
      ->setPrompt('Vyberte úroveň kurzu')
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