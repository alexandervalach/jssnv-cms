<?php

namespace App\Presenters;

/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter {

    public function renderDefault() {
        $this->template->bannerItems = $this->bannerRepository->findAll();
    }

}
