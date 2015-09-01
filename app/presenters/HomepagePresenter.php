<?php

namespace App\Presenters;

/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter {

    public function renderDefault() {
        $section = $this->sectionRepository->findByValue('name', 'Základné informácie')->fetch();
        $this->template->banner = $this->bannerRepository->findAll()->order("order");
        $this->template->section = $section;
        $this->template->post = $section->related('post');
    }

}
