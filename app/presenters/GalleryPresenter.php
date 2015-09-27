<?php

namespace App\Presenters;

use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;

class GalleryPresenter extends BasePresenter {

    /** @var Selection */
    private $gallerySelection;

    /** @var string */
    private $error = "Gallery not found!";

    public function actionView($id) {
        $this->gallerySelection = $this->galleryRepository->findByValue('album_id', $id);
    }

    public function renderView($id) {
        if (!$this->gallerySelection) {
            $this->template->images = null;
        } else {
            $this->template->images = $this->galleryRepository->findByValue('album_id', $id);
        }
        $this->template->imgFolder = $this->imgFolder;
    }

}
