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
    } catch (AbortException $e) {

    }
  }

  public function renderAll (): void
  {
    $this->template->branches = $this->branchesRepository->findAll();
  }

  public function actionView (int $id): void
  {

  }

  public function renderView (int $id): void
  {

  }

  protected function createComponentBranchForm (): Form
  {
    return $this->branchFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->submittedAddForm($values);
    });
  }

  public function submittedAddForm (ArrayHash $values): void
  {
    try {
      $this->guestRedirect();
      $this->branchesRepository->insert($values);
    } catch (AbortException $e) {
      $this->flashMessage('Something went wrong', 'danger');
    }
  }

}