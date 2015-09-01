<?php

namespace App\Presenters;

use App\Components\ModalDialog;
use App\Model\PostRepository;
use App\Model\SectionRepository;
use App\Model\BannerRepository;
use Nette\Application\UI\Presenter;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Presenter {

    /** @var PostRepository */
    protected $postRepository;

    /** @var SectionRepository */
    protected $sectionRepository;

    /** @var BannerRepository */
    protected $bannerRepository;

    public function __construct(PostRepository $postRepository, SectionRepository $sectionRepository, BannerRepository $bannerRepository) {
        parent::__construct();
        $this->postRepository = $postRepository;
        $this->sectionRepository = $sectionRepository;
        $this->bannerRepository = $bannerRepository;
    }

    public function beforeRender() {
        $this->template->sections = $this->sectionRepository->findAll();
    }

    protected function userIsLogged() {
        if (!$this->user->isLoggedIn()) {
            $this->redirect('Sign:in#nav');
        }
    }

    protected function createComponentModalDialog() {
        return new ModalDialog();
    }

}
