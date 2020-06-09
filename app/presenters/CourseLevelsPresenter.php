<?php

namespace App\Presenters;

use App\Components\BreadcrumbControl;
use App\Forms\CourseLevelFormFactory;
use App\Forms\SearchFormFactory;
use App\Model\AlbumsRepository;
use App\Model\CourseLevelsRepository;
use App\Model\SectionsRepository;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Database\Table\ActiveRow;
use Nette\Forms\Form;
use Nette\Utils\ArrayHash;

class CourseLevelsPresenter extends BasePresenter
{
  /**
   * @var CourseLevelFormFactory
   */
  private $courseLevelFormFactory;

  /***
   * @var ActiveRow
   */
  private $courseLevelRow;

  /**
   * @var CourseLevelsRepository
   */
  private $courseLevelsRepository;

  const THEME_TITLE = 'Úrovne kurzov';

  public function __construct(AlbumsRepository $albumsRepository, SectionsRepository $sectionRepository, BreadcrumbControl $breadcrumbControl, SearchFormFactory $searchForm, CourseLevelsRepository $courseLevelsRepository, CourseLevelFormFactory $courseLevelFormFactory)
  {
    parent::__construct($albumsRepository, $sectionRepository, $breadcrumbControl, $searchForm);
    $this->courseLevelsRepository = $courseLevelsRepository;
    $this->courseLevelFormFactory = $courseLevelFormFactory;
  }

  public function actionAll (): void
  {
    try {
      $this->guestRedirect();
      $this['breadcrumb']->add(self::THEME_TITLE);
    } catch (AbortException $e) {
      // $this->flashMessage(self::UNKNOWN_ERROR, self::ERROR);
    }
  }

  public function renderAll (): void
  {
    $this->template->courseLevels = $this->courseLevelsRepository->findAll();
  }

  public function actionView (int $id): void
  {
    $this->courseLevelRow = $this->courseLevelsRepository->findById($id);

    if (!$this->courseLevelRow) {
      throw new BadRequestException(self::ITEM_NOT_FOUND);
    }

    try {
      $this->guestRedirect();
      $this['courseLevelForm']->setDefaults($this->courseLevelRow);
    } catch (AbortException $e) {
      $this->flashMessage(self::UNKNOWN_ERROR, self::ERROR);
    }
  }

  public function renderView (int $id): void
  {
    $this['breadcrumb']->add(self::THEME_TITLE, $this->link('all'));
    $this['breadcrumb']->add($this->courseLevelRow->label);
    $this->template->courseLevel = $this->courseLevelRow;
  }

  protected function createComponentCourseLevelForm (): Form
  {
    return $this->courseLevelFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->getParameter('id') ? $this->submittedEditForm($values) : $this->submittedAddForm($values);
    });
  }

  public function submittedAddForm (ArrayHash $values): void
  {
    try {
      $this->guestRedirect();
      $this->courseLevelsRepository->insert($values);
      $this->flashMessage(self::ITEM_ADDED, self::SUCCESS);
      $this->redirect('all');
    } catch (AbortException $e) {
      // $this->flashMessage(self::UNKNOWN_ERROR, self::ERROR);
    }
  }

  public function submittedEditForm (ArrayHash $values): void
  {
    try {
      $this->guestRedirect();
      $this->courseLevelRow->update($values);
      $this->flashMessage(self::ITEM_UPDATED, self::SUCCESS);
      $this->redirect('view', $this->courseLevelRow->id);
    } catch (AbortException $e) {
      // $this->flashMessage(self::UNKNOWN_ERROR, self::ERROR);
    }
  }
}