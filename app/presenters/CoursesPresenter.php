<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\BreadcrumbControl;
use app\forms\CourseFormFactory;
use App\Forms\SearchFormFactory;
use App\Model\AlbumsRepository;
use App\Model\CoursesRepository;
use App\Model\SectionsRepository;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\ArrayHash;

class CoursesPresenter extends BasePresenter
{
  /**
   * @var CourseFormFactory
   */
  private $courseFormFactory;

  /**
   * @var CoursesRepository
   */
  private $coursesRepository;

  /** @var ActiveRow */
  private $courseRow;

  const THEME_TITLE = 'Kurzy';

  public function actionView (int $id): void
  {
    $this->courseRow = $this->coursesRepository->findById($id);

    if (!$this->courseRow) {
      throw new BadRequestException(self::ITEM_NOT_FOUND);
    }

    $this->guestRedirect();
    $this['breadcrumb']->add(self::THEME_TITLE, $this->link('all'));
    $this['breadcrumb']->add($this->courseRow->label);
    $this['courseForm']->setDefaults($this->courseRow);
  }

  public function renderView (int $id): void
  {
    $this->template->course = $this->courseRow;
  }

  public function actionAll (): void
  {
    $this->guestRedirect();
    $this['breadcrumb']->add(self::THEME_TITLE);
  }

  public function renderAll (): void
  {
    $this->template->courses = $this->coursesRepository->findAll();
  }

  public function __construct(AlbumsRepository $albumsRepository, SectionsRepository $sectionRepository, BreadcrumbControl $breadcrumbControl, SearchFormFactory $searchForm, CoursesRepository $coursesRepository, CourseFormFactory $courseFormFactory)
  {
    parent::__construct($albumsRepository, $sectionRepository, $breadcrumbControl, $searchForm);
    $this->coursesRepository = $coursesRepository;
    $this->courseFormFactory = $courseFormFactory;
  }

  protected function createComponentCourseForm (): Form
  {
    return $this->courseFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->getParameter('id') ? $this->submittedEditForm($values) : $this->submittedAddForm($values);
    });
  }

  public function submittedAddForm (ArrayHash $values): void
  {
    $this->guestRedirect();
    $this->coursesRepository->insert($values);
    $this->flashMessage(self::ITEM_ADDED, self::SUCCESS);
    $this->redirect('all');
  }

  public function submittedEditForm (ArrayHash $values): void
  {
    $this->guestRedirect();
    $this->courseRow->update($values);
    $this->flashMessage(self::ITEM_UPDATED, self::SUCCESS);
    $this->redirect('view', $this->courseRow->id);
  }
}