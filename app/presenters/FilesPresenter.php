<?php

namespace App\Presenters;

use App\FormHelper;
use App\Forms\RemoveFormFactory;
use App\Forms\UploadFormFactory;
use App\Model\AlbumsRepository;
use App\Model\FilesRepository;
use App\Model\SectionsRepository;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Nette\Utils\FileSystem;

/**
 * Class FilesPresenter
 * @package App\Presenters
 */
class FilesPresenter extends BasePresenter {

  /** @var ActiveRow */
  private $fileRow;

  /** @var string */
  private $error = "File not found!";

  /**
   * @var UploadFormFactory
   */
  private $uploadFormFactory;

  /**
   * @var FilesRepository
   */
  private $filesRepository;

  /**
   * @var RemoveFormFactory
   */
  private $removeFormFactory;

  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              UploadFormFactory $uploadFormFactory,
                              FilesRepository $filesRepository,
                              RemoveFormFactory $removeFormFactory)
  {
    parent::__construct($albumsRepository, $sectionRepository);
    $this->filesRepository = $filesRepository;
    $this->uploadFormFactory = $uploadFormFactory;
    $this->removeFormFactory = $removeFormFactory;
  }

  /**
   * @throws \Nette\Application\AbortException
   */
  public function actionAll() {
    $this->userIsLogged();
  }

  /**
   *
   */
  public function renderAll() {
    $this->template->files = $this->filesRepository->findAll()->order('name ASC');
    $this->template->fileFolder = $this->fileFolder;
  }

  /**
   * @param $id
   * @throws BadRequestException
   * @throws \Nette\Application\AbortException
   */
  public function actionRemove($id) {
    $this->userIsLogged();
    $this->fileRow = $this->filesRepository->findById($id);

    if (!$this->fileRow) {
      throw new BadRequestException($this->error);
    }
  }

  /**
   * @param $id
   */
  public function renderRemove($id) {
    $this->template->file = $this->fileRow;
  }

  protected function createComponentUploadForm () {
    return $this->uploadFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->userIsLogged();
      $this->filesRepository->softDelete($this->fileRow->id);
      $this->redirect('all');
    });
  }

  /**
   * @return Form
   */
  protected function createComponentRemoveFileForm() {
    return $this->removeFormFactory->create(function () {
      $this->userIsLogged();
      $this->filesRepository->softDelete($this->fileRow->id);
      $this->flashMessage(self::ITEM_REMOVED, self::SUCCESS);
      $this->redirect('all');
    }, function () {
      $this->redirect('all');
    });
  }

  /**
   * @throws \Nette\Application\AbortException
   */
  public function formCancelled() {
    $this->redirect('Posts:show#primary', $this->fileRow->ref('posts', 'post_id'));
  }

  /**
   * @throws \Nette\Application\AbortException
   */
  public function submittedFileRemoveForm() {
    $this->userIsLogged();
    $id = $this->fileRow->ref('posts', 'post_id');
    $this->filesRepository->softDelete($this->fileRow->id);
    $this->redirect('Posts:show#primary', $id);
  }

}
