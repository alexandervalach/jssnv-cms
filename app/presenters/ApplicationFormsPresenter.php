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

  public function actionAll (): void
  {
    try {
      $this->guestRedirect();
    } catch (AbortException $e) {
    }
  }

  public function renderAll (): void
  {
    $this['breadcrumb']->add('Prihlášky');
    $this->template->applicationForms = $this->applicationFormsRepository->findAll();
  }

  public function actionView (int $id): void
  {

  }

  public function renderView (int $id): void
  {

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

  public function renderSuccess (String $name): void
  {
    $this->template->name = $name;
  }

  protected function createComponentApplicationForm (): Form
  {
    return $this->applicationFormFactory->create($this->branchRow->id, function (Form $form, array $values) {
      $this->submittedApplicationForm($values);
    });
  }

  public function submittedApplicationForm (array $values): void
  {
    $data = [];

    foreach ($values['branch_class_id'] as $classId) {
      $appFormData = $values;
      $appFormData['branch_class_id'] = $classId;
      $data[] = $appFormData;
    }

    $this->applicationFormsRepository->insert($data);

    try {
      $this->redirect('success', $values['name']);
    } catch (AbortException $e) {

    }
  }
}