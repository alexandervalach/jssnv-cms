<?php

namespace App\Presenters;

/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter {

    public function renderDefault() {
        $this->template->bannerItems = $this->bannerRepository->findAll();
        $this->template->posts = $this->postRepository->findByValue('onHomepage', 1);
        $this->template->subPosts = $this->subPostRepository->findByValue('onHomepage', 1);
    }

}
