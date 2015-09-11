<?php

namespace App\Presenters;

use App\Components\ModalDialog;
use App\Model\PostRepository;
use App\Model\SectionRepository;
use App\Model\BannerRepository;
use App\Model\SubPostRepository;
use App\Model\SubSectionRepository;
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
    
    /** @var subPostRepository */
    protected $subPostRepository;
    
    /** @var $subSectionRepository */
    protected $subSectionRepository;

    public function __construct(PostRepository $postRepository, SectionRepository $sectionRepository, BannerRepository $bannerRepository, subPostRepository $subPostRepository, subSectionRepository $subSectionRepository) {
        parent::__construct();
        $this->postRepository = $postRepository;
        $this->sectionRepository = $sectionRepository;
        $this->bannerRepository = $bannerRepository;
        $this->subPostRepository = $subPostRepository;
        $this->subSectionRepository = $subSectionRepository;
    }

    public function beforeRender() {
        $this->template->sections = $this->sectionRepository->findAll()->order("order DESC");
    }

    protected function userIsLogged() {
        if (!$this->user->isLoggedIn()) {
            $this->redirect('Sign:in');
        }
    }

    protected function createComponentModalDialog() {
        return new ModalDialog();
    }

}