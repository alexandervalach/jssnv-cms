<?php

namespace App\Presenters;

use App\FormHelper;
use App\Model\AlbumsRepository;
use App\Model\ImagesRepository;
use App\Model\SectionsRepository;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\FileSystem;
use Nette\Utils\Image;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\UnknownImageFileException;

/**
 * Class ImagesPresenter
 * @package App\Presenters
 */
class ImagesPresenter extends BasePresenter {

  /** @var string */
  private $error = "Image not found!";

  /** @var string */
  private $storage = 'images/';

  /** @var ActiveRow */
  private $albumRow;

  /** @var ActiveRow */
  private $imageRow;

  /**
   * @var ImagesRepository
   */
  private $imagesRepository;

  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              ImagesRepository $imagesRepository)
  {
    parent::__construct($albumsRepository, $sectionRepository);
    $this->imagesRepository = $imagesRepository;
  }

  /**
   * @param $id
   * @throws BadRequestException
   */
  public function actionView($id) {
    $this->albumRow = $this->albumsRepository->findById($id);

    if (!$this->albumRow) {
      throw new BadRequestException("Album not found!");
    }
  }

  /**
   * @param $id
   */
  public function renderView($id) {
    $this->template->images = $this->imagesRepository->findByValue('album_id', $id)->order('height ASC');
    $this->template->imgFolder = $this->imgFolder;
    $this->template->mainAlbum = $this->albumRow;
  }

  /**
   * @param $id
   * @throws BadRequestException
   */
  public function actionAdd($id) {
    $this->albumRow = $this->albumsRepository->findById($id);

    if (!$this->albumRow) {
      throw new BadRequestException("Album not found!");
    }
  }

  /**
   * @param $id
   */
  public function renderAdd($id) {
    $this->template->images = $this->imagesRepository->findByValue('album_id', $id);
    $this->template->imgFolder = $this->imgFolder;
    $this->template->mainAlbum = $this->albumRow;
  }

  /**
   * @param $id
   * @throws BadRequestException
   * @throws AbortException
   */
  public function actionRemove($id) {
    $this->userIsLogged();
    $this->imageRow = $this->imagesRepository->findById($id);

    if (!$this->imageRow) {
      throw new BadRequestException($this->error);
    }
  }

  /**
   * @param $id
   */
  public function renderRemove($id) {
    $this->template->img = $this->imageRow;
    $this['removeImageForm'];
  }

  /**
   * @return Form
   */
  protected function createComponentUploadImagesForm() {
    $form = new Form;
    $form->addUpload('images', 'Vyber obrázky', true);
    $form->addSubmit('upload', 'Nahraj')
        ->onClick[] = [$this, 'submittedUploadImagesForm'];
    $form->addSubmit('cancel', 'Zrušiť')
        ->setHtmlAttribute('class', 'btn btn-warning')
        ->onClick[] = [$this, 'formReturnToGallery'];
    FormHelper::setBootstrapRenderer($form);
    return $form;
  }

  /**
   * @return Form
   */
  protected function createComponentRemoveImageForm() {
    $form = new Form;
    $form->addSubmit('remove', 'Odstrániť')
        ->setHtmlAttribute('class', 'btn btn-danger')
        ->onClick[] = [$this, 'submittedRemoveImageForm'];

    $form->addSubmit('cancel', 'Zrušiť')
        ->setHtmlAttribute('class', 'btn btn-warning')
        ->onClick[] = [$this, 'formCancelled'];

    FormHelper::setBootstrapRenderer($form);
    return $form;
  }

  /**
   * @param SubmitButton $btn
   * @throws AbortException
   * @throws UnknownImageFileException
   */
  public function submittedUploadImagesForm(SubmitButton $btn) {
    $this->userIsLogged();

    $values = $btn->form->getValues();
    $imgData = array();

    foreach ($values['images'] as $img) {
      $name = strtolower($img->getSanitizedName());

      if ($img->isOk() AND $img->isImage()) {
        $img->move($this->storage . $name);

        $image = Image::fromFile('images/' . $name);
        $height = $image->getHeight();
        $width = $image->getWidth();

        $imgData['name'] = $name;
        $imgData['album_id'] = $this->albumRow;
        $imgData['width'] = $width;
        $imgData['height'] = $height;
        $this->imagesRepository->insert($imgData);
      }
    }

    $this->flashMessage('Fotografie boli nahraté');
    $this->redirect('view', $this->albumRow);
  }

  /**
   * @throws AbortException
   */
  public function submittedRemoveForm() {
    $this->userIsLogged();

    $albumRow = $this->albumRow;
    $gallerySelection = $this->albumRow->related('gallery');

    if ($gallerySelection != NULL) {
      foreach ($gallerySelection as $gallery) {
        $gallery->delete();
      }
    }

    $albumRow->delete();
    $this->flashMessage('Album bol odstránený');
    $this->redirect('Homepage:');
  }

  /**
   * @throws AbortException
   */
  public function submittedRemoveImageForm() {
    $this->userIsLogged();
    $id = $this->imageRow->ref('album', 'album_id');
    $this->imageRow->delete();
    $this->flashMessage('Fotografia bola odstránená');
    $this->redirect('view#primary', $id);
  }

  /**
   * @throws AbortException
   */
  public function formCancelled() {
    $this->redirect('view', $this->imageRow->ref('album', 'album_id'));
  }

  /**
   * @throws AbortException
   */
  public function formReturnToGallery() {
    $this->redirect('view', $this->albumRow);
  }

}
