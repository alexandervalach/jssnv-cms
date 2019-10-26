<?php

namespace App\Presenters;

use App\FormHelper;
use App\Forms\ModalRemoveFormFactory;
use App\Forms\PostFormFactory;
use App\Forms\UploadFormFactory;
use App\Model\AlbumsRepository;
use App\Model\FilesRepository;
use App\Model\PostsRepository;
use App\Model\SectionsRepository;
use Nette\Database\Table\ActiveRow;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;

/**
 * Class PostsPresenter
 * @package App\Presenters
 */
class PostsPresenter extends BasePresenter {

  /** @var ActiveRow */
  private $postRow;

  /** @var ActiveRow */
  private $sectionRow;

  /** @var string */
  protected $storage = "files/";

  /** @var string */
  private $error = "Post not found!";

  /**
   * @var PostsRepository
   */
  private $postsRepository;

  /**
   * @var PostFormFactory
   */
  private $postFormFactory;

  /**
   * @var FilesRepository
   */
  private $filesRepository;

  /**
   * @var UploadFormFactory
   */
  private $uploadFormFactory;

  /**
   * @var ModalRemoveFormFactory
   */
  private $removeFormFactory;

  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              PostsRepository $postsRepository,
                              PostFormFactory $postFormFactory,
                              FilesRepository $filesRepository,
                              UploadFormFactory $uploadFormFactory,
                              ModalRemoveFormFactory $modalRemoveFormFactory)
  {
    parent::__construct($albumsRepository, $sectionRepository);
    $this->postsRepository = $postsRepository;
    $this->filesRepository = $filesRepository;
    $this->postFormFactory = $postFormFactory;
    $this->uploadFormFactory = $uploadFormFactory;
    $this->removeFormFactory = $modalRemoveFormFactory;
  }

  /**
   * @param $id
   * @throws BadRequestException
   */
  public function actionShow ($id) {
    $this->postRow = $this->postsRepository->findByValue('section_id', $id)->fetch();

    if (!$this->postRow || !$this->postRow->is_present) {
      throw new BadRequestException($this->error);
    }

    $this->sectionRow = $this->postRow->ref('section_id');
    $this['editForm']->setDefaults($this->postRow);
  }

  /**
   *
   */
  public function renderShow () {
    $this->template->post = $this->postRow;
    $this->template->files = $this->filesRepository->findByValue('post_id', $this->postRow);
    $this->template->fileFolder = $this->storage;
  }

  /**
   * @return Form
   */
  protected function createComponentEditForm() {
    return $this->postFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->userIsLogged();
      $this->postRow->update($values);

      if ($this->sectionRow->name != $values->name) {
        $this->sectionRow->update( array('name' => $values->name) );
      }

      $this->flashMessage(self::ITEM_UPDATED, self::SUCCESS);
      $this->redirect('show', $this->postRow->section_id);
    });
  }

  protected function createComponentRemoveForm () {
    return $this->removeFormFactory->create(function () {
      $this->userIsLogged();
      $sectionRow = $this->postRow->ref('sections', 'section_id');
      $this->sectionsRepository->softDelete($sectionRow->id);
      $this->postsRepository->softDelete($this->postRow->id);
      $this->flashMessage('Sekcia bola odstránená.', 'alert-success');
      $this->redirect('Sections:all');
    });
  }

  /**
   * @return Form
   */
  protected function createComponentUploadForm() {
    return $this->uploadFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->userIsLogged();

      foreach ($values->files as $file) {
        $name = strtolower($file->getSanitizedName());

        if ($file->isOk()) {
          $file->move($this->storage . $name);

          $fileData = [];
          $fileData['name'] = $name;
          $fileData['post_id'] = $this->postRow->id;
          $this->filesRepository->insert($fileData);
        }
      }

      $this->flashMessage(self::FILES_UPLOADED, self::SUCCESS);
      $this->redirect('show', $this->postRow->id);
    });
  }

}
