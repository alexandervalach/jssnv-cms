<?php

namespace App\Presenters;

use App\FormHelper;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\FileSystem;
use Nette\Utils\Image;

class GalleryPresenter extends BasePresenter {

    /** @var string */
    private $error = "Gallery not found!";

    /** @var string */
    private $storage = 'images/';

    /** @var ActiveRow */
    private $albumRow;

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

    protected function createComponentUploadImagesForm() {
        $form = new Form;
        $form->addUpload('images', 'Vyber obrázky', true);
        $form->addSubmit('upload', 'Nahraj');
        $form->onSuccess[] = $this->submittedUploadImagesForm;
        FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    public function submittedUploadImagesForm(Form $form) {
        $this->userIsLogged();
        $values = $form->getValues();
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

}
