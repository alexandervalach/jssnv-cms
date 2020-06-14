<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\BreadcrumbControl;
use App\Forms\ApplicationFormFactory;
use App\Forms\SearchFormFactory;
use App\Helpers\ApplicationHelper;
use App\Model\AlbumsRepository;
use App\Model\ApplicationFormsRepository;
use App\Model\BranchesClassesRepository;
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

  /**
   * @var mixed
   */
  private $applicationForm;

  /**
   * @var BranchesClassesRepository
   */
  private $branchClassesRepository;

  /**
   * @var array|mixed
   */
  private $branchClasses;

  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              BreadcrumbControl $breadcrumbControl,
                              SearchFormFactory $searchForm,
                              BranchesRepository $branchesRepository,
                              BranchesClassesRepository $branchClassesRepository,
                              ApplicationFormsRepository $applicationFormsRepository,
                              ApplicationFormFactory $applicationFormFactory)
  {
    parent::__construct($albumsRepository, $sectionRepository, $breadcrumbControl, $searchForm);
    $this->branchesRepository = $branchesRepository;
    $this->applicationFormsRepository = $applicationFormsRepository;
    $this->branchClassesRepository = $branchClassesRepository;
    $this->applicationFormFactory = $applicationFormFactory;
  }

  public function actionAll (): void
  {
    $this->guestRedirect();
  }

  public function renderAll (): void
  {
    $this['breadcrumb']->add('Prihlášky');
    $items = $this->applicationFormsRepository->fetchAll();
    $this->template->applicationForms = ApplicationHelper::setAppFormsStyle($items);
  }

  public function actionView (int $id): void
  {
    $this->guestRedirect();

    $this->applicationForm = $this->applicationFormsRepository->fetch($id);

    if (!$this->applicationForm) {
      throw new BadRequestException(self::ITEM_NOT_FOUND);
    }
  }

  public function renderView (int $id): void
  {
    $this['breadcrumb']->add('Prihlášky', $this->link('all'));
    $this['breadcrumb']->add($this->applicationForm->name);
    $this->template->appForm = ApplicationHelper::setAppFormStyle($this->applicationForm);
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

  public function actionAdd (int $id): void
  {
    $this->branchRow = $this->branchesRepository->findById($id);

    if (!$this->branchRow) {
      throw new BadRequestException(self::ITEM_NOT_FOUND);
    }

    $this->branchClasses = $this->branchClassesRepository->fetchForApplicationForm($this->branchRow->id);
  }

  public function renderAdd (int $id): void
  {
    $this['breadcrumb']->add('Prihlášky', $this->link('ApplicationForms:all'));
    $this['breadcrumb']->add($this->branchRow->label);

    $this->template->items = $this->branchClasses;
    $this->template->branch = $this->branchRow;
  }

  public function renderSuccess (String $name): void
  {
    $this->template->name = $name;
  }

  protected function createComponentApplicationForm (): Form
  {
    return $this->applicationFormFactory->create(function (Form $form, array $values) {
      $classes = $form->getHttpData($form::DATA_TEXT | $form::DATA_KEYS, 'branch_class_id[]');
      $this->submittedApplicationForm($values, $classes);
    });
  }

  public function submittedApplicationForm (array $values, $classes): void
  {
    $data = [];

    foreach ($classes as $classId) {
      $appFormData = $values;
      $appFormData['branch_class_id'] = $classId;
      $data[] = $appFormData;
    }

    $this->applicationFormsRepository->insert($data);
    $this->flashMessage('Prihláška bola úspešne podaná', self::SUCCESS);
    $this->redirect(
      'success',
      ApplicationHelper::parseName($values['name'], $values['title_bn'], $values['title_an'])
    );
  }

  private function submittedUpdateStatusApplicationForm (string $status)
  {
    $this->guestRedirect();
    $this->applicationFormRow->update([ 'status' => $status ]);
    // $this->flashMessage(self::ITEM_UPDATED, self::INFO);

    if ($this->getParameter('id')) {
      $this->redirect('view', $this->applicationFormRow->id);
    } else {
      $this->redirect('all');
    }
  }

  private function submittedRemoveApplicationForm ()
  {
    $this->guestRedirect();
    $this->applicationFormsRepository->softDelete($this->applicationFormRow->id);
    $this->flashMessage(self::ITEM_REMOVED, self::INFO);
    $this->redirect('all');
  }
}