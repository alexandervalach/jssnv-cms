<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\BreadcrumbControl;
use App\Forms\ApplicationFormFactory;
use App\Forms\SearchFormFactory;
use App\Model\AlbumsRepository;
use App\Model\ApplicationFormsRepository;
use App\Model\BranchesRepository;
use App\Model\SectionsRepository;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\BadRequestException;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\ArrayHash;

class ApplicationFormsPresenter extends BasePresenter
{
  /**
   * @var BranchesRepository
   */
  private $branchesRepository;

  /**
   * @var mixed|ActiveRow|null
   */
  private $branchRow;

  /**
   * @var ApplicationFormFactory
   */
  private $applicationFormFactory;

  /**
   * @var ApplicationFormsRepository
   */
  private $applicationFormsRepository;

  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              BreadcrumbControl $breadcrumbControl,
                              SearchFormFactory $searchForm,
                              BranchesRepository $branchesRepository,
                              ApplicationFormsRepository $applicationFormsRepository,
                              ApplicationFormFactory $applicationFormFactory)
  {
    parent::__construct($albumsRepository, $sectionRepository, $breadcrumbControl, $searchForm);
    $this->branchesRepository = $branchesRepository;
    $this->applicationFormsRepository = $applicationFormsRepository;
    $this->applicationFormFactory = $applicationFormFactory;
  }

  public function renderAll (): void
  {
    $this['breadcrumb']->add('Prihlášky');
    $this->template->branches = $this->branchesRepository->findAll();
  }

  public function actionAdd (int $id): void
  {
    $this->branchRow = $this->branchesRepository->findById($id);

    if (!$this->branchRow) {
      throw new BadRequestException(self::ITEM_NOT_FOUND);
    }
  }

  public function renderAdd (int $id): void
  {
    $this['breadcrumb']->add('Prihlášky', $this->link('ApplicationForms:all'));
    $this['breadcrumb']->add($this->branchRow->label);
    $this->template->branch = $this->branchRow;
  }

  protected function createComponentApplicationForm (): Form
  {
    return $this->applicationFormFactory->create($this->branchRow->id, function (Form $form, ArrayHash $values) {
      $this->submittedApplicationForm($values);
    });
  }

  public function submittedApplicationForm (ArrayHash $values): void
  {
    $this->applicationFormsRepository->insert($values);
    try {
      $this->redirect('all');
    } catch (AbortException $e) {

    }
  }
}