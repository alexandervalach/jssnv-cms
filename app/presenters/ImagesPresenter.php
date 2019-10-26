<?php

namespace App\Presenters;

use App\FormHelper;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\FileSystem;
use Nette\Utils\Image;
use Nette\Forms\Controls\SubmitButton;

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
  private $galleryRow;

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
    $this->template->images = $this->galleryRepository->findByValue('album_id', $id)->order('height ASC');
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
    $this->template->images = $this->galleryRepository->findByValue('album_id', $id);
    $this->template->imgFolder = $this->imgFolder;
    $this->template->mainAlbum = $this->albumRow;
  }

  /**
   * @param $id
   * @throws BadRequestException
   * @throws \Nette\Application\AbortException
   */
  public function actionRemove($id) {
    $this->userIsLogged();
    $this->galleryRow = $this->galleryRepository->findById($id);

    if (!$this->galleryRow) {
      throw new BadRequestException($this->error);
    }
  }

  /**
   * @param $id
   */
  public function renderRemove($id) {
    $this->template->img = $this->galleryRow;
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
   * @throws \Nette\Application\AbortException
   * @throws \Nette\Utils\UnknownImageFileException
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
        $this->galleryRepository->insert($imgData);
      }
    }

    $this->flashMessage('Fotografie boli nahraté');
    $this->redirect('view#primary', $this->albumRow);
  }

  /**
   * @throws \Nette\Application\AbortException
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
   * @throws \Nette\Application\AbortException
   */
  public function submittedRemoveImageForm() {
    $this->userIsLogged();
    $id = $this->galleryRow->ref('album', 'album_id');
    $this->galleryRow->delete();
    $this->flashMessage('Fotografia bola odstránená');
    $this->redirect('view#primary', $id);
  }

  /**
   * @throws \Nette\Application\AbortException
   */
  public function formCancelled() {
    $this->redirect('view#primary', $this->galleryRow->ref('album', 'album_id'));
  }

  /**
   * @throws \Nette\Application\AbortException
   */
  public function formReturnToGallery() {
    $this->redirect('view#primary', $this->albumRow);
  }

}
