<?php

namespace App\Presenters;

use App\Components\ModalDialog;
use App\Model\AlbumRepository;
use App\Model\BannerRepository;
use App\Model\GalleryRepository;
use App\Model\ImagesRepository;
use App\Model\NoticeRepository;
use App\Model\PostRepository;
use App\Model\SectionRepository;
use App\Model\SubPostRepository;
use App\Model\SubSectionRepository;
use App\Model\UserRepository;
use Nette\Application\UI\Presenter;
use Nette\Application\UI\Form;
use App\FormHelper;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Presenter {

    /** @var AlbumRepository */
    protected $albumRepository;

    /** @var BannerRepository */
    protected $bannerRepository;

    /** @var GalleryRepository */
    protected $galleryRepository;

    /** @var ImagesRepository */
    protected $imagesRepository;

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

    /** @var */
    protected $imgFolder = "images/";

    public function __construct(AlbumRepository $albumRepository, BannerRepository $bannerRepository, GalleryRepository $galleryRepository, ImagesRepository $imageRepository, PostRepository $postRepository, SectionRepository $sectionRepository, SubPostRepository $subPostRepository, SubSectionRepository $subSectionRepository, NoticeRepository $noticeRepository, UserRepository $userRepository) {
        parent::__construct();
        $this->albumRepository = $albumRepository;
        $this->bannerRepository = $bannerRepository;
        $this->galleryRepository = $galleryRepository;
        $this->imagesRepository = $imageRepository;
        $this->postRepository = $postRepository;
        $this->sectionRepository = $sectionRepository;
        $this->subPostRepository = $subPostRepository;
        $this->subSectionRepository = $subSectionRepository;
        $this->noticeRepository = $noticeRepository;
        $this->userRepository = $userRepository;
    }

    public function beforeRender() {
        $this->template->sections = $this->sectionRepository->findAll()->order("order DESC");
        $this->template->albums = $this->albumRepository->findAll();
        $this->template->imgFolder = $this->imgFolder;
    }

    protected function userIsLogged() {
        if (!$this->user->isLoggedIn()) {
            $this->redirect('Sign:in');
        }
    }

    protected function createComponentModalDialog() {
        return new ModalDialog();
    }

    protected function createComponentRemoveForm() {
        $form = new Form();
        $form->addSubmit('remove', 'Odstrániť')
                        ->getControlPrototype()->class = "btn btn-danger";
        $form->onSuccess[] = $this->submittedRemoveForm;
        FormHelper::setBootstrapRenderer($form);
        return $form;
    }

}
