<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\BreadcrumbControl;
use App\Forms\SearchFormFactory;
use App\Forms\TextContentFormFactory;
use App\Forms\FileUpdateFormFactory;
use App\Helpers\FileHelper;
use App\Model\AlbumsRepository;
use App\Model\ContentsRepository;
use App\Model\SectionsRepository;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Database\Table\ActiveRow;
use Nette\Forms\Form;
use Nette\Utils\ArrayHash;

class ContentsPresenter extends BasePresenter
{
  /**
   * @var ContentsRepository
   */
  private $contentsRepository;

  /**
   * @var ActiveRow|null
   */
  private $contentRow;

  /**
   * @var TextContentFormFactory
   */
  private $textContentFormFactory;

  /**
   * @var FileUpdateFormFactory
   */
  private $fileUpdateFormFactory;

  /**
   * @var ActiveRow|null
   */
  private $sectionRow;

  /**
   * ContentsPresenter constructor.
   * @param AlbumsRepository $albumsRepository
   * @param SectionsRepository $sectionRepository
   * @param BreadcrumbControl $breadcrumbControl
   * @param SearchFormFactory $searchForm
   * @param ContentsRepository $contentsRepository
   */
  public function __construct(AlbumsRepository $albumsRepository, SectionsRepository $sectionRepository, BreadcrumbControl $breadcrumbControl, SearchFormFactory $searchForm, ContentsRepository $contentsRepository, TextContentFormFactory $textContentFormFactory, FileUpdateFormFactory $fileUpdateFormFactory)
  {
    parent::__construct($albumsRepository, $sectionRepository, $breadcrumbControl, $searchForm);
    $this->contentsRepository = $contentsRepository;
    $this->textContentFormFactory = $textContentFormFactory;
    $this->fileUpdateFormFactory = $fileUpdateFormFactory;
  }

  /**
   * @param int $id
   * @throws AbortException
   * @throws BadRequestException
   */
  public function actionRemove(int $id): void
  {
    $this->guestRedirect();
    $this->contentRow = $this->contentsRepository->findById($id);

    if (!$this->contentRow) {
      $this->error(self::ITEM_NOT_FOUND);
    }

    $this->submittedRemoveForm($id);
  }

  /**
   * @param int $id
   * @throws AbortException
   */
  private function submittedRemoveForm(int $id): void
  {
    $this->contentsRepository->softDelete($this->contentRow);
    $this->flashMessage(self::ITEM_REMOVED, self::INFO);
    $this->redirect('Sections:view', $this->contentRow->section_id);
  }

  /**
   * @param int $id
   * @throws AbortException
   * @throws BadRequestException
   */
  public function actionText(int $id): void
  {
    $this->guestRedirect();
    $this->contentRow = $this->contentsRepository->findById($id);

    if (!$this->contentRow) {
      $this->error(self::ITEM_NOT_FOUND);
    }

    $this->sectionRow = $this->sectionsRepository->findById((int) $this->contentRow->section_id);

    if (!$this->sectionRow) {
      $this->error(self::ITEM_NOT_FOUND);
    }

    $this['textContentForm']->setDefaults($this->contentRow);
  }

  /**
   * @param int $id
   */
  public function renderText(int $id): void
  {
    $this->template->content = $this->contentRow;
    $this->template->section = $this->sectionRow;
  }

  /**
   * Generates edit text form control
   * @return Form
   */
  protected function createComponentTextContentForm(): Form
  {
    return $this->textContentFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->contentRow->update($values);
      $this->flashMessage(self::ITEM_UPDATED, self::INFO);
      $this->redirect('Sections:view', $this->contentRow->section_id);
    });
  }

  /**
   * @param int $id
   * @throws AbortException
   * @throws BadRequestException
   */
  public function actionFile(int $id): void
  {
    $this->guestRedirect();
    $this->contentRow = $this->contentsRepository->findById($id);

    if (!$this->contentRow) {
      $this->error(self::ITEM_NOT_FOUND);
    }

    $this->sectionRow = $this->sectionsRepository->findById((int) $this->contentRow->section_id);

    if (!$this->sectionRow) {
      $this->error(self::ITEM_NOT_FOUND);
    }

    $this['fileUpdateForm']->setDefaults($this->contentRow);
  }

  /**
   * @param int $id
   */
  public function renderFile(int $id): void
  {
    $this->template->content = $this->contentRow;
    $this->template->section = $this->sectionRow;
  }

  /**
   * Generates update file form control
   * @return Form
   */
  protected function createComponentFileUpdateForm(): Form
  {
    return $this->fileUpdateFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->submittedFileUpdateForm($values);
    });
  }

  private function submittedFileUpdateForm(ArrayHash $values): void
  {
    try {
      $fileData = FileHelper::uploadFile($values->file);
    } catch (InvalidArgumentException $e) {
      $this->flashMessage($e->getMessage(), self::ERROR);
      $this->redirect('view', $this->sectionRow->id);
    } catch (IOException $e) {
      $this->flashMessage($e->getMessage(), self::ERROR);
      $this->redirect('view', $this->sectionRow->id);
    }

    $data = [
      'title' => $values->title,
      'section_id' => $this->sectionRow->id,
      'type' => ContentsRepository::$type['file']
    ];

    if ($fileData) {
      $data['text'] = $fileData['file_name'];
    }

    $this->contentRow->update($data);
    $this->flashMessage(self::ITEMS_ADDED, self::INFO);
    $this->redirect('Sections:view', $this->sectionRow->id);
  }
}