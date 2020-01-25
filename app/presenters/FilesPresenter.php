<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\BreadcrumbControl;
use App\Forms\RemoveFormFactory;
use App\Forms\SearchFormFactory;
use App\Forms\UploadFormFactory;
use App\Model\AlbumsRepository;
use App\Model\ContentsRepository;
use App\Model\SectionsRepository;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\ArrayHash;

/**
 * Class FilesPresenter
 * @package App\Presenters
 */
class FilesPresenter extends BasePresenter
{
  /** @var ActiveRow */
  private $fileRow;

  /**
   * @var UploadFormFactory
   */
  private $uploadFormFactory;

  /**
   * @var ContentsRepository
   */
  private $filesRepository;

  /**
   * @var RemoveFormFactory
   */
  private $removeFormFactory;

  /**
   * FilesPresenter constructor.
   * @param AlbumsRepository $albumsRepository
   * @param SectionsRepository $sectionRepository
   * @param BreadcrumbControl $breadcrumbControl
   * @param SearchFormFactory $searchFormFactory
   * @param UploadFormFactory $uploadFormFactory
   * @param ContentsRepository $filesRepository
   * @param RemoveFormFactory $removeFormFactoryText
   */
  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              BreadcrumbControl $breadcrumbControl,
                              SearchFormFactory $searchFormFactory,
                              UploadFormFactory $uploadFormFactory,
                              ContentsRepository $filesRepository,
                              RemoveFormFactory $removeFormFactoryText)
  {
    parent::__construct($albumsRepository, $sectionRepository, $breadcrumbControl, $searchFormFactory);
    $this->filesRepository = $filesRepository;
    $this->uploadFormFactory = $uploadFormFactory;
    $this->removeFormFactory = $removeFormFactoryText;
  }

  /**
   * Prepares data for template
   * @throws AbortException
   */
  public function actionAll(): void
  {
    $this->guestRedirect();
  }

  /**
   * Passes data to all template
   */
  public function renderAll(): void
  {
    $this->template->files = $this->filesRepository->findAll();
  }

  /**
   * @param $id
   * @throws BadRequestException
   * @throws AbortException
   */
  public function actionRemove(int $id): void
  {
    $this->guestRedirect();
    $this->fileRow = $this->filesRepository->findById($id);

    if (!$this->fileRow) {
      throw new BadRequestException(self::FILE_NOT_FOUND);
    }

    $this->submittedFileRemoveForm();
  }

  /**
   * @return Form
   */
  protected function createComponentUploadForm (): Form
  {
    return $this->uploadFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->guestRedirect();
      $this->filesRepository->softDelete((int)$this->fileRow->id);
      $this->redirect('all');
    });
  }

  /**
   * @return Form
   */
  protected function createComponentRemoveFileForm(): Form
  {
    return $this->removeFormFactory->create(function () {
      $this->guestRedirect();
      $this->filesRepository->softDelete((int)$this->fileRow->id);
      $this->flashMessage(self::ITEM_REMOVED, self::SUCCESS);
      $this->redirect('all');
    }, function () {
      $this->redirect('all');
    });
  }

  /**
   * @throws AbortException
   */
  public function formCancelled(): void
  {
    $this->redirect('Posts:view#primary', $this->fileRow->ref('posts', 'post_id'));
  }

  /**
   * @throws AbortException
   */
  public function submittedFileRemoveForm(): void
  {
    $this->guestRedirect();
    $id = $this->fileRow->ref('posts', 'post_id');
    $this->filesRepository->softDelete((int)$this->fileRow->id);
    $this->redirect('Posts:view#primary', $id);
  }

}
