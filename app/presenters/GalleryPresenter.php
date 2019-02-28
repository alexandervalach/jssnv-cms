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

        if (!$this->albumRow) {
            throw new BadRequestException("Album not found!");
        }
    }

    public function renderView($id) {
        $this->template->images = $this->galleryRepository->findByValue('album_id', $id)->order('height ASC');
        $this->template->imgFolder = $this->imgFolder;
        $this->template->mainAlbum = $this->albumRow;
    }

    public function actionAdd($id) {
        $this->albumRow = $this->albumRepository->findById($id);

        if (!$this->albumRow) {
            throw new BadRequestException("Album not found!");
        }
    }

    public function renderAdd($id) {
        $this->template->images = $this->galleryRepository->findByValue('album_id', $id);
        $this->template->imgFolder = $this->imgFolder;
        $this->template->mainAlbum = $this->albumRow;
    }

    public function actionRemove($id) {
        $this->userIsLogged();
        $this->galleryRow = $this->galleryRepository->findById($id);

        if (!$this->galleryRow) {
            throw new BadRequestException($this->error);
        }
    }

    public function renderRemove($id) {
        $this->template->img = $this->galleryRow;
        $this->getComponent('removeImageForm');
    }

    protected function createComponentUploadImagesForm() {
        $form = new Form;
        $form->addUpload('images', 'Vyber obrázky', true);
        
        $form->addSubmit('upload', 'Nahraj')
                ->onClick[] = [$this, 'submittedUploadImagesForm'];
        
        $form->addSubmit('cancel', 'Zrušiť')
                ->setAttribute('class', 'btn btn-warning')
                ->onClick[] = [$this, 'formReturnToGallery'];

        FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    protected function createComponentRemoveImageForm() {
        $form = new Form;
        $form->addSubmit('remove', 'Odstrániť')
                ->setAttribute('class', 'btn btn-danger')
                ->onClick[] = [$this, 'submittedRemoveImageForm'];

        $form->addSubmit('cancel', 'Zrušiť')
                ->setAttribute('class', 'btn btn-warning')
                ->onClick[] = [$this, 'formCancelled'];

        FormHelper::setBootstrapRenderer($form);
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

        $this->flashMessage('Fotografie boli nahraté');
        $this->redirect('view#primary', $this->albumRow);
    }

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

    public function submittedRemoveImageForm() {
        $this->userIsLogged();

        $id = $this->galleryRow->ref('album', 'album_id');
        $this->galleryRow->delete();
        
        $this->flashMessage('Fotografia bola odstránená');
        $this->redirect('view#primary', $id);
    }

    public function formCancelled() {
        $this->redirect('view#primary', $this->galleryRow->ref('album', 'album_id'));
    }
    
    public function formReturnToGallery() {
        $this->redirect('view#primary', $this->albumRow);
    }

}
