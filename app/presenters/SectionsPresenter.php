<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\BreadcrumbControl;
use App\Forms\FileUploadFormFactory;
use App\Forms\MultiUploadFormFactory;
use App\Forms\TextContentFormFactory;
use App\Forms\ModalRemoveFormFactory;
use App\Forms\SearchFormFactory;
use App\Forms\SectionFormFactory;
use App\Helpers\FileHelper;
use App\Helpers\ImageHelper;
use App\Model\AlbumsRepository;
use App\Model\ContentsRepository;
use App\Model\SectionsRepository;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\GroupedSelection;
use Nette\InvalidArgumentException;
use Nette\IOException;
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
   * @var TextContentFormFactory
   */
  private $textContentFormFactory;

  /**
   * @var ContentsRepository
   */
  private $contentsRepository;

  /**
   * @var GroupedSelection
   */
  private $contents;

  /**
   * @var MultiUploadFormFactory
   */
  private $multiUploadFormFactory;

  /**
   * @var FileUploadFormFactory
   */
  private $fileUploadFormFactory;

  /**
   * SectionsPresenter constructor.
   * @param AlbumsRepository $albumsRepository
   * @param SectionsRepository $sectionRepository
   * @param BreadcrumbControl $breadcrumbControl
   * @param SearchFormFactory $searchFormFactory
   * @param SectionFormFactory $sectionFormFactory
   * @param ModalRemoveFormFactory $modalRemoveFormFactory
   * @param TextContentFormFactory $textContentFormFactory
   * @param ContentsRepository $contentsRepository
   * @param MultiUploadFormFactory $multiUploadFormFactory
   * @param FileUploadFormFactory $fileUploadFormFactory
   */
  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              BreadcrumbControl $breadcrumbControl,
                              SearchFormFactory $searchFormFactory,
                              SectionFormFactory $sectionFormFactory,
                              ModalRemoveFormFactory $modalRemoveFormFactory,
                              TextContentFormFactory $textContentFormFactory,
                              ContentsRepository $contentsRepository,
                              MultiUploadFormFactory $multiUploadFormFactory,
                              FileUploadFormFactory $fileUploadFormFactory)
  {
    parent::__construct($albumsRepository, $sectionRepository, $breadcrumbControl, $searchFormFactory);
    $this->sectionFormFactory = $sectionFormFactory;
    $this->contentsRepository = $contentsRepository;
    $this->modalRemoveFormFactory = $modalRemoveFormFactory;
    $this->textContentFormFactory = $textContentFormFactory;
    $this->multiUploadFormFactory = $multiUploadFormFactory;
    $this->fileUploadFormFactory = $fileUploadFormFactory;
  }

  /**
   * @throws AbortException
   */
  public function actionAll(): void
  {
    $this->guestRedirect();
    $this['breadcrumb']->add('Sekcie');
  }

  /**
   *
   */
  public function renderAll(): void
  {
    // Property $sections is inherited from parent
    $this->template->sections = $this->sectionsRepository->findAll();
  }

  public function actionView(int $id): void
  {
    $this->sectionRow = $this->sectionsRepository->findById($id);

    if (!$this->sectionRow) {
      try {
        $this->error(self::ITEM_NOT_FOUND);
      } catch (BadRequestException $e) {
        $this->flashMessage(self::ITEM_NOT_FOUND, self::ERROR);
        $this->redirect('Homepage:');
      }
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
      $this->guestRedirect();

      // Delete also subsections if parent section
      if ($this->sectionRow->section_id != null) {
        $subSections = $this->sectionsRepository->findByParent($this->sectionRow->id);
        foreach ($subSections as $subSection) {
          $this->sectionsRepository->softDelete($subSection);
        }
      }

      // Delete section from database
      $this->sectionsRepository->softDelete($this->sectionRow);
      $this->flashMessage(self::ITEM_REMOVED);
      $this->redirect('all');
    });
  }

  protected function createComponentTextContentForm (): Form
  {
    return $this->textContentFormFactory->create(function (Form $form, ArrayHash $values) {
      // Change the type according to content
      $values['type'] = ContentsRepository::$type['text'];
      $values['section_id'] = $this->sectionRow->id;
      $this->contentsRepository->insert($values);
      $this->flashMessage(self::ITEM_ADDED, self::SUCCESS);
      $this->redirect('view', $this->sectionRow->id);
    });
  }

  protected function createComponentUploadImagesForm (): Form
  {
    return $this->multiUploadFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->guestRedirect();
      $this->submittedUploadImagesForm($values);
    });
  }

  protected function createComponentUploadFilesForm (): Form
  {
    return $this->fileUploadFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->guestRedirect();
      $this->submittedUploadFilesForm($values);
    });
  }

  /**
   * @param ArrayHash $values
   * @throws AbortException
   */
  private function submittedAddForm (ArrayHash $values): void
  {
    $this->guestRedirect();
    $values->offsetSet('section_id', $values->section_id === 0 ? null : $values->section_id);
    $section = $this->sectionsRepository->insert($values);
    $this->flashMessage(self::ITEM_ADDED);
    $this->redirect('view', $section->id);
  }

  /**
   * @param ArrayHash $values
   * @throws AbortException
   */
  private function submittedEditForm (ArrayHash $values): void
  {
    $this->guestRedirect();
    $values->section_id = $values->section_id === 0 ? null : $values->section_id;
    $this->sectionRow->update($values);
    $this->flashMessage(self::ITEM_UPDATED, self::SUCCESS);
    $this->redirect('view', $this->sectionRow->id);
  }

  private function submittedUploadImagesForm(ArrayHash $values): void
  {
    $imageNames = [];

    try {
      $imageNames = ImageHelper::uploadImages($values->images);
    } catch (InvalidArgumentException $e) {
      $this->flashMessage($e->getMessage(), self::ERROR);
      $this->redirect('view', $this->sectionRow->id);
    } catch (IOException $e) {
      $this->flashMessage($e->getMessage(), self::ERROR);
      $this->redirect('view', $this->sectionRow->id);
    }

    $data = [];
    foreach ($imageNames as $imageName) {
      $data[] = [
        'text' => $imageName,
        'section_id' => $this->sectionRow->id,
        'type' => ContentsRepository::$type['image']
      ];
    }

    $this->contentsRepository->insert($data);
    $this->flashMessage(self::ITEMS_ADDED, self::INFO);
    $this->redirect('view', $this->sectionRow->id);
  }

  private function submittedUploadFilesForm(ArrayHash $values): void
  {
    $files = [];

    try {
      $files = FileHelper::uploadFiles($values->files);
    } catch (InvalidArgumentException $e) {
      $this->flashMessage($e->getMessage(), self::ERROR);
      $this->redirect('view', $this->sectionRow->id);
    } catch (IOException $e) {
      $this->flashMessage($e->getMessage(), self::ERROR);
      $this->redirect('view', $this->sectionRow->id);
    }

    $data = [];
    foreach ($files as $file) {
      $data[] = [
        'title' => $file['title'],
        'text' => $file['base_name'],
        'section_id' => $this->sectionRow->id,
        'type' => ContentsRepository::$type['file']
      ];
    }

    $this->contentsRepository->insert($data);
    $this->flashMessage(self::ITEMS_ADDED, self::INFO);
    $this->redirect('view', $this->sectionRow->id);
  }

}
