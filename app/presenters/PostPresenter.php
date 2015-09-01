<?php

namespace App\Presenters;

use Nette\Database\Table\ActiveRow;

class PostPresenter extends BasePresenter {

    /** @var ActiveRow */
    private $postRow;

    /** @var ActiveRow */
    private $sectionRow;

    public function actionShow($id) {
        $this->sectionRow = $this->sectionRepository->findById($id);
        $this->postRow = $this->sectionRow->related('post')->fetch();
    }

    public function renderShow() {
        $this->template->section = $this->sectionRow;
        $this->template->post = $this->postRow;
    }

}
