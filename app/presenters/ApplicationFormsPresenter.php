<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\BreadcrumbControl;
use App\Forms\ApplicationFormFactory;
use App\Forms\SearchFormFactory;
use App\Model\AlbumsRepository;
use App\Model\BranchesRepository;
use App\Model\SectionsRepository;

class ApplicationFormsPresenter extends BasePresenter
{
  /**
   * @var BranchesRepository
   */
  private $branchesRepository;

  /**
   * @var mixed|\Nette\Database\Table\ActiveRow|null
   */
  private $branchRow;

  public function __construct(AlbumsRepository $albumsRepository, SectionsRepository $sectionRepository, BreadcrumbControl $breadcrumbControl, SearchFormFactory $searchForm, BranchesRepository $branchesRepository)
  {
    parent::__construct($albumsRepository, $sectionRepository, $breadcrumbControl, $searchForm);
    $this->branchesRepository = $branchesRepository;
  }

  public function actionAll (): void
  {

  }

  public function renderAll (): void
  {
    $this['breadcrumb']->add('Prihlášky');
    $this->template->branches = $this->branchesRepository->findAll();
  }

  public function actionAdd (int $id): void
  {
    $this->branchRow = $this->branchesRepository->findById($id);
  }

  public function renderAdd (int $id): void
  {
    $this['breadcrumb']->add('Prihlášky', $this->link('ApplicationForms:all'));
    $this['breadcrumb']->add($this->branchRow->label);
    // $this->template->branches = $this->branchesRepository->findAll();
  }

  /*
  protected function createComponentApplicationForm (): Form
  {
    return $this->applicationFormFactory->create()
  }
  */
}