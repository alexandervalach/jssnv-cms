<?php

namespace App\Presenters;

/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter {

  /**
   *
   */
  public function renderDefault() {
    $this->template->slides = $this->slidesRepository->findAll();
    $this->template->posts = $this->postsRepository->findByValue('onHomepage', 1);
  }

}
