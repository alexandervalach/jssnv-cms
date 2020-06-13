<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\BreadcrumbControl;
use App\Forms\ApplicationFormFactory;
use App\Forms\SearchFormFactory;
use App\Helpers\ApplicationHelper;
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

  /**
   * @var mixed|ActiveRow|null
   */
  private $applicationFormRow;

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
    $items = $this->applicationFormsRepository->fetchApplications();
    $this->template->applicationForms = ApplicationHelper::setAppFormsStyle($items);
  }

  public function actionView (int $id): void
  {

  }

  public function renderView (int $id): void
  {

  }

  public function actionRemove (int $id): void
  {
    $this->guestRedirect();
    $this->applicationFormRow = $this->applicationFormsRepository->findById($id);

    if (!$this->applicationFormRow) {
      throw new BadRequestException(self::ITEM_NOT_FOUND);
    }

    $this->submittedRemoveApplicationForm();
  }

  public function actionUpdateStatus (int $id, string $status): void
  {
    $this->guestRedirect();
    $this->applicationFormRow = $this->applicationFormsRepository->findById($id);

    if (!$this->applicationFormRow) {
      throw new BadRequestException(self::ITEM_NOT_FOUND);
    }

    $this->submittedUpdateStatusApplicationForm($status);
  }

  public function actionCancel (int $id): void
  {
    $this->guestRedirect();
    $this->applicationFormRow = $this->applicationFormsRepository->findById($id);

    if (!$this->applicationFormRow) {
      throw new BadRequestException(self::ITEM_NOT_FOUND);
    }

    $this->submittedCancelApplicationForm();
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

  private function submittedUpdateStatusApplicationForm (string $status)
  {
    $this->guestRedirect();
    $this->applicationFormRow->update([ 'status' => $status ]);
    // $this->flashMessage(self::ITEM_UPDATED, self::INFO);
    $this->redirect('all');
  }

  private function submittedRemoveApplicationForm ()
  {
    $this->guestRedirect();
    $this->applicationFormsRepository->softDelete($this->applicationFormRow->id);
    $this->flashMessage(self::ITEM_REMOVED, self::INFO);
    $this->redirect('all');
  }
}