<?php

namespace App\Presenters;

use App\FormHelper;
use Nette\Database\Table\ActiveRow;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;

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
        if(!$this->albumRow) {
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
            }

            $imgData['name'] = $name;
            $imgData['album_id'] = $this->albumRow;
            $this->galleryRepository->insert($imgData);
        }
        $this->redirect('view#primary', $this->albumRow);
    }

}
