<?php

namespace App\Presenters;

/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter {

    public function renderDefault() {
        $this->template->banner = $this->bannerRepository->findAll()->order("order");
    }

}
