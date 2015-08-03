<?php

namespace App\Presenters;

use Nette\Database\Table\ActiveRow;

class PostsPresenter extends BasePresenter{
    /** @var ActiveRow */
    private $postRow;
    
    public function actionShow() {
        $this->postRow = $this->postRepository->findAll();
    }
    
    public function renderShow() {
        $this->template->posts = $this->postRow;
    }
}
