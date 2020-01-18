<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\BreadcrumbControl;
use App\Forms\AddTextContentFormFactory;
use App\Forms\ModalRemoveFormFactory;
use App\Forms\RemoveFormFactory;
use App\Forms\SectionFormFactory;
use App\Model\AlbumsRepository;
use App\Model\ContentsRepository;
use App\Model\SectionsRepository;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\GroupedSelection;
use Nette\Utils\ArrayHash;

/**
 * Class SectionsPresenter
 * @package App\Presenters
 */
class SectionsPresenter extends BasePresenter
{
  /** @var ActiveRow */
  private $sectionRow;

  /**
   * @var SectionFormFactory
   */
  private $sectionFormFactory;

  /**
   * @var ModalRemoveFormFactory
   */
  private $modalRemoveFormFactory;

  /**
   * @var AddTextContentFormFactory
   */
  private $addTextContentFormFactory;

  /**
   * @var ContentsRepository
   */
  private $contentsRepository;

  /**
   * @var GroupedSelection
   */
  private $contents;

  /**
   * SectionsPresenter constructor.
   * @param AlbumsRepository $albumsRepository
   * @param SectionsRepository $sectionRepository
   * @param SectionFormFactory $sectionFormFactory
   * @param ModalRemoveFormFactory $modalRemoveFormFactory
   * @param AddTextContentFormFactory $addTextContentFormFactory
   * @param ContentsRepository $contentsRepository
   * @param BreadcrumbControl $breadcrumbControl
   */
  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              SectionFormFactory $sectionFormFactory,
                              ModalRemoveFormFactory $modalRemoveFormFactory,
                              AddTextContentFormFactory $addTextContentFormFactory,
                              ContentsRepository $contentsRepository,
                              BreadcrumbControl $breadcrumbControl)
  {
    parent::__construct($albumsRepository, $sectionRepository, $breadcrumbControl);
    $this->sectionFormFactory = $sectionFormFactory;
    $this->contentsRepository = $contentsRepository;
    $this->modalRemoveFormFactory = $modalRemoveFormFactory;
    $this->addTextContentFormFactory = $addTextContentFormFactory;
  }

  /**
   * @throws AbortException
   */
  public function actionAll(): void
  {
    $this->userIsLogged();
  }

  /**
   *
   */
  public function renderAll(): void
  {
    // Property $sections is inherited from parent
    $this->template->sections = $this->sections;
    $this['breadcrumb']->add('Sekcie');
  }

  public function actionView(int $id): void
  {
    $this->sectionRow = $this->sectionsRepository->findById($id);

    if (!$this->sectionRow) {
      throw new BadRequestException(self::ITEM_NOT_FOUND);
    }

    $this->contents = $this->sectionRow->related('contents')->where('is_present', 1);
    $this['sectionForm']->setDefaults($this->sectionRow);

    // Breadcrumb management
    if ($this->user->loggedIn) {
      $this['breadcrumb']->add('Sekcie', $this->link('all'));
    }

    $parent = $this->sectionRow->ref('sections', 'section_id');

    if ($parent) {
      $this['breadcrumb']->add($parent->name, $this->link('Sections:view', $parent->id));
    }

    $this['breadcrumb']->add($this->sectionRow->name);
  }

  public function renderView(int $id): void
  {
    $this->template->section = $this->sectionRow;
    $this->template->contents = $this->contents;
  }

  /**
   * @return Form
   */
  protected function createComponentSectionForm(): Form
  {
    return $this->sectionFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->getParameter('id') ? $this->submittedEditForm($values) : $this->submittedAddForm($values);
    });
  }

  /**
   * @return Form
   */
  protected function createComponentRemoveForm(): Form
  {
    return $this->modalRemoveFormFactory->create(function () {
      $this->userIsLogged();

      // Delete also subsections if parent section
      if ($this->sectionRow->section_id != null) {
        $subSections = $this->sectionsRepository->findByParent($this->sectionRow->id);
        foreach ($subSections as $subSection) {
          $this->sectionsRepository->softDelete($subSection->id);
        }
      }

      // Delete section from database
      $this->sectionsRepository->softDelete((int)$this->sectionRow->id);
      $this->flashMessage(self::ITEM_REMOVED);
      $this->redirect('all');
    });
  }

  protected function createComponentAddTextContentForm (): Form
  {
    return $this->addTextContentFormFactory->create(function (Form $form, ArrayHash $values) {
      // Change the type according to content
      $values['type'] = ContentsRepository::$type['text'];
      $values['section_id'] = $this->sectionRow->id;
      $this->contentsRepository->insert($values);
      $this->flashMessage(self::ITEM_ADDED, self::SUCCESS);
      $this->redirect('view', $this->sectionRow->id);
    });
  }

  /**
   * @param ArrayHash $values
   * @throws AbortException
   */
  private function submittedAddForm(ArrayHash $values): void
  {
    $this->userIsLogged();
    $values->offsetSet('section_id', $values->section_id === 0 ? null : $values->section_id);
    $section = $this->sectionsRepository->insert($values);
    $this->flashMessage(self::ITEM_ADDED);
    $this->redirect('view', $section->id);
  }

  /**
   * @param ArrayHash $values
   * @throws AbortException
   */
  private function submittedEditForm(ArrayHash $values): void
  {
    $this->userIsLogged();
    $values->section_id = $values->section_id === 0 ? null : $values->section_id;
    $this->sectionRow->update($values);
    $this->flashMessage(self::ITEM_UPDATED, self::SUCCESS);
    $this->redirect('view', $this->sectionRow->id);
  }

}
