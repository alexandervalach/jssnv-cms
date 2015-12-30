<?php

namespace App\Presenters;

use App\FormHelper;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\FileSystem;
use Nette\Utils\Image;
use Nette\Forms\Controls\SubmitButton;

class GalleryPresenter extends BasePresenter {

    /** @var string */
    private $error = "Image not found!";

    /** @var string */
    private $storage = 'images/';

    /** @var ActiveRow */
    private $albumRow;

    /** @var ActiveRow */
    private $galleryRow;

    public function actionView($id) {
        $this->albumRow = $this->albumRepository->findById($id);
    }

    public function renderView($id) {
        if (!$this->albumRow) {
            throw new BadRequestException("Album not found!");
        }
        $this->template->images = $this->galleryRepository->findByValue('album_id', $id)->order('height ASC');
        $this->template->imgFolder = $this->imgFolder;
        $this->template->album = $this->albumRow;
        $this->getComponent('uploadImagesForm');
        $this->getComponent('removeForm');
    }

    public function actionAdd($id) {
        $this->albumRow = $this->albumRepository->findById($id);
    }

    public function renderAdd($id) {
        if (!$this->albumRow) {
            throw new BadRequestException("Album not found!");
        }
        $this->template->images = $this->galleryRepository->findByValue('album_id', $id);
        $this->template->imgFolder = $this->imgFolder;
        $this->template->album = $this->albumRow;
        $this->getComponent('uploadImagesForm');
    }

    public function actionRemove($id) {
        $this->userIsLogged();
        $this->galleryRow = $this->galleryRepository->findById($id);
    }

    public function renderRemove($id) {
        if (!$this->galleryRow) {
            throw new BadRequestException($this->error);
        }
        $this->template->img = $this->galleryRow;
        $this->getComponent('removeImageForm');
    }

    protected function createComponentUploadImagesForm() {
        $form = new Form;
        $form->addUpload('images', 'Vyber obrázky', true);
        $form->addSubmit('upload', 'Nahraj')
                ->onClick[] = $this->submittedUploadImagesForm;
        $form->addSubmit('cancel', 'Zrušiť')
                ->setAttribute('class', 'btn btn-warning')
                ->onClick[] = $this->formReturnToGallery;
        FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    protected function createComponentRemoveImageForm() {
        $form = new Form;
        $form->addSubmit('remove', 'Odstrániť')
                ->setAttribute('class', 'btn btn-danger')
                ->onClick[] = $this->submittedRemoveImageForm;
        $form->addSubmit('cancel', 'Zrušiť')
                ->setAttribute('class', 'btn btn-warning')
                ->onClick[] = $this->formCancelled;
        return $form;
    }

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
        $this->redirect('view#primary', $this->albumRow);
    }

    public function submittedRemoveForm() {
        $albumRow = $this->albumRow;
        $gallerySelection = $this->albumRow->related('gallery');

        if ($gallerySelection != NULL) {
            foreach ($gallerySelection as $gallery) {
                $img = new FileSystem;
                $img->delete($this->imgFolder . $gallery->name);
                $gallery->delete();
            }
        }

        $albumRow->delete();
        $this->flashMessage('Album bol odstránený.');
        $this->redirect('Homepage:');
    }

    public function submittedRemoveImageForm() {
        $this->userIsLogged();
        $img = $this->galleryRow;
        $id = $img->ref('album', 'album_id');
        $imgFile = new FileSystem;
        $imgFile->delete($this->imgFolder . $img->name);
        $img->delete();
        $this->redirect('view#primary', $id);
    }

    public function formCancelled() {
        $this->redirect('view#primary', $this->galleryRow->ref('album', 'album_id'));
    }
    
    public function formReturnToGallery() {
        $this->redirect('view#primary', $this->albumRow);
    }

}
