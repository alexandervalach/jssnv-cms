<?php

namespace App\Presenters;

use App\Components\ModalDialog;
use App\Model\BannerRepository;
use App\Model\NoticeRepository;
use App\Model\PostRepository;
use App\Model\SectionRepository;
use App\Model\SubPostRepository;
use App\Model\SubSectionRepository;
use App\Model\UserRepository;
use Nette\Application\UI\Presenter;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Presenter {

    /** @var BannerRepository */
    protected $bannerRepository;

    /** @var NoticeRepository */
    protected $noticeRepository;

    /** @var PostRepository */
    protected $postRepository;

    /** @var SectionRepository */
    protected $sectionRepository;

    /** @var SubPostRepository */
    protected $subPostRepository;

    /** @var SubSectionRepository */
    protected $subSectionRepository;

    /** @var UserRepository */
    protected $userRepository;
    
    public function __construct(PostRepository $postRepository, SectionRepository $sectionRepository, BannerRepository $bannerRepository, SubPostRepository $subPostRepository, SubSectionRepository $subSectionRepository, NoticeRepository $noticeRepository, UserRepository $userRepository) {
        parent::__construct();
        $this->postRepository = $postRepository;
        $this->sectionRepository = $sectionRepository;
        $this->bannerRepository = $bannerRepository;
        $this->subPostRepository = $subPostRepository;
        $this->subSectionRepository = $subSectionRepository;
        $this->noticeRepository = $noticeRepository;
        $this->userRepository = $userRepository;
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
