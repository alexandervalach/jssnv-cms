<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\BreadcrumbControl;
use App\Forms\BranchFormFactory;
use App\Forms\SearchFormFactory;
use App\Model\AlbumsRepository;
use App\Model\BranchesRepository;
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

  public function __construct(AlbumsRepository $albumsRepository, SectionsRepository $sectionRepository, BreadcrumbControl $breadcrumbControl, SearchFormFactory $searchForm, BranchesRepository $branchesRepository, BranchFormFactory $branchFormFactory)
  {
    parent::__construct($albumsRepository, $sectionRepository, $breadcrumbControl, $searchForm);
    $this->branchesRepository = $branchesRepository;
    $this->branchFormFactory = $branchFormFactory;
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
  }

  protected function createComponentBranchForm (): Form
  {
    return $this->branchFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->getParameter('id') ? $this->submittedEditForm($values) : $this->submittedAddForm($values);
    });
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