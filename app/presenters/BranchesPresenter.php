<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\BreadcrumbControl;
use App\Forms\BranchFormFactory;
use App\Forms\ClassFormFactory;
use App\Forms\SearchFormFactory;
use App\Model\AlbumsRepository;
use App\Model\BranchesClassesRepository;
use App\Model\BranchesRepository;
use App\Model\ClassesRepository;
use App\Model\SectionsRepository;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Database\Table\ActiveRow;
use Nette\Forms\Form;
use Nette\Utils\ArrayHash;

class BranchesPresenter extends BasePresenter
{
  /**
   * @var BranchesRepository
   */
  private $branchesRepository;

  /**
   * @var BranchFormFactory
   */
  private $branchFormFactory;

  /***
   * @var ActiveRow
   */
  private $branchRow;

  const THEME_TITLE = 'Pobočky';

  /**
   * @var BranchesClassesRepository
   */
  private $branchesClassesRepository;

  /**
   * @var ClassFormFactory
   */
  private $classFormFactory;

  /**
   * @var ClassesRepository
   */
  private $classesRepository;

  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              BreadcrumbControl $breadcrumbControl,
                              SearchFormFactory $searchForm,
                              BranchesRepository $branchesRepository,
                              BranchesClassesRepository $branchesClassesRepository,
                              ClassesRepository $classesRepository,
                              BranchFormFactory $branchFormFactory,
                              ClassFormFactory $classFormFactory)
  {
    parent::__construct($albumsRepository, $sectionRepository, $breadcrumbControl, $searchForm);
    $this->branchesRepository = $branchesRepository;
    $this->branchesClassesRepository = $branchesClassesRepository;
    $this->classesRepository = $classesRepository;
    $this->branchFormFactory = $branchFormFactory;
    $this->classFormFactory = $classFormFactory;
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
    $this->template->branches = $this->branchesRepository->findAll();
  }

  public function actionView (int $id): void
  {
    $this->branchRow = $this->branchesRepository->findById($id);

    if (!$this->branchRow) {
      throw new BadRequestException(self::ITEM_NOT_FOUND);
    }

    try {
      $this->guestRedirect();
      $this['branchForm']->setDefaults($this->branchRow);
    } catch (AbortException $e) {
      $this->flashMessage(self::UNKNOWN_ERROR, self::ERROR);
    }
  }

  public function renderView (int $id): void
  {
    $this['breadcrumb']->add(self::THEME_TITLE, $this->link('all'));
    $this['breadcrumb']->add($this->branchRow->label);
    $this->template->branch = $this->branchRow;
    $this->template->items = ArrayHash::from($this->branchesClassesRepository->getForBranch($this->branchRow->id));
  }

  protected function createComponentBranchForm (): Form
  {
    return $this->branchFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->getParameter('id') ? $this->submittedEditForm($values) : $this->submittedAddForm($values);
    });
  }

  protected function createComponentClassForm (): Form
  {
    return $this->classFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->submittedAddClassForm($values);
    });
  }

  public function submittedAddClassForm (ArrayHash $values): void
  {
    try {
      $this->guestRedirect();
      $class = $this->classesRepository->insert($values);

      $this->branchesClassesRepository->insert(
        [
          'branch_id' => $this->branchRow->id,
          'class_id' => $class->id
        ]
      );

      $this->flashMessage(self::ITEM_ADDED, self::SUCCESS);
      $this->redirect('all');
    } catch (AbortException $e) {
      // $this->flashMessage(self::UNKNOWN_ERROR, self::ERROR);
    }
  }

  public function submittedAddForm (ArrayHash $values): void
  {
    try {
      $this->guestRedirect();
      $this->branchesRepository->insert($values);
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
      $this->branchRow->update($values);
      $this->flashMessage(self::ITEM_UPDATED, self::SUCCESS);
      $this->redirect('view', $this->branchRow->id);
    } catch (AbortException $e) {
      // $this->flashMessage(self::UNKNOWN_ERROR, self::ERROR);
    }
  }

}