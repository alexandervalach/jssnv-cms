<?php

namespace App\Presenters;

use App\Components\ModalDialog;
use App\Components\FormCancelled;
use App\Model\AlbumRepository;
use App\Model\AnswersRepository;
use App\Model\BannerRepository;
use App\Model\FilesRepository;
use App\Model\GalleryRepository;
use App\Model\ImagesRepository;
use App\Model\LevelsRepository;
use App\Model\NoticeRepository;
use App\Model\PostRepository;
use App\Model\QuestionsRepository;
use App\Model\ResultsRepository;
use App\Model\SectionRepository;
use App\Model\SubFilesRepository;
use App\Model\SubPostRepository;
use App\Model\SubSectionRepository;
use App\Model\TestsRepository;
use App\Model\TestsResultsRepository;
use App\Model\UserRepository;
use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\Presenter;
use Nette\Application\UI\Form;
use App\FormHelper;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Presenter {

    const TEST_NOT_FOUND = 'Test not found';
    const ITEM_ADD_SUCCESS = 'Položka bola pridaná';
    const ITEM_EDIT_SUCCESS = 'Položka bola upravená';
    const QUESTIONS = 'questions';

    /** @var AlbumRepository */
    protected $albumRepository;

    /** @var AnswersRepository */
    protected $answersRepository;

    /** @var BannerRepository */
    protected $bannerRepository;

    /** @var FilesRepository */
    protected $filesRepository;

    /** @var GalleryRepository */
    protected $galleryRepository;

    /** @var ImagesRepository */
    protected $imagesRepository;

    /** @var LevelsRepository */
    protected $levelsRepository;

    /** @var NoticeRepository */
    protected $noticeRepository;

    /** @var PostRepository */
    protected $postRepository;

    /** @var QuestionsRepository */
    protected $questionsRepository;

    /** @var ResultsRepository */
    protected $resultsRepository;

    /** @var SectionRepository */
    protected $sectionRepository;

    /** @var SubFilesRepository */
    protected $subFilesRepository;

    /** @var SubPostRepository */
    protected $subPostRepository;

    /** @var SubSectionRepository */
    protected $subSectionRepository;

    /** @var TestsRepository */
    protected $testsRepository;

    /** @var TestsResultsRepository */
    protected $testsResultsRepository;

    /** @var UserRepository */
    protected $userRepository;

    /** @var string */
    protected $imgFolder = "images/";

    /** @var string */
    protected $fileFolder = "files/";

    public function __construct(AlbumRepository $albumRepository,
        AnswersRepository $answersRepository,
        BannerRepository $bannerRepository,
        FilesRepository $filesRepository,
        GalleryRepository $galleryRepository,
        ImagesRepository $imageRepository,
        LevelsRepository $levelsRepository,
        NoticeRepository $noticeRepository,
        PostRepository $postRepository,
        QuestionsRepository $questionsRepository,
        ResultsRepository $resultsRepository,
        SectionRepository $sectionRepository,
        SubFilesRepository $subFilesRepository,
        SubPostRepository $subPostRepository,
        SubSectionRepository $subSectionRepository,
        TestsRepository $testsRepository,
        TestsResultsRepository $testsResultsRepository,
        UserRepository $userRepository) {
        parent::__construct();
        $this->albumRepository = $albumRepository;
        $this->answersRepository = $answersRepository;
        $this->bannerRepository = $bannerRepository;
        $this->filesRepository = $filesRepository;
        $this->galleryRepository = $galleryRepository;
        $this->imagesRepository = $imageRepository;
        $this->levelsRepository = $levelsRepository;
        $this->postRepository = $postRepository;
        $this->questionsRepository = $questionsRepository;
        $this->resultsRepository = $resultsRepository;
        $this->sectionRepository = $sectionRepository;
        $this->subFilesRepository = $subFilesRepository;
        $this->subPostRepository = $subPostRepository;
        $this->subSectionRepository = $subSectionRepository;
        $this->noticeRepository = $noticeRepository;
        $this->testsRepository = $testsRepository;
        $this->testsResultsRepository = $testsResultsRepository;
        $this->userRepository = $userRepository;
    }

    public function beforeRender() {
        $this->template->menuSections = $this->sectionRepository->findByValue('visible', 1)->order("order DESC");
        $this->template->menuAlbums = $this->albumRepository->findAll();
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

    protected function createComponentFormCancelled() {
        return new FormCancelled();
    }

    protected function createComponentRemoveForm() {
        $form = new Form();

        $form->addSubmit('cancel', 'Zrušiť')
            ->setAttribute('class', 'btn btn-warning')
            ->setAttribute('data-dismiss', 'modal');

        $form->addSubmit('remove', 'Odstrániť')
            ->setAttribute('class', 'btn btn-danger');

        $form->onSuccess[] = [$this, 'submittedRemoveForm'];

        FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    protected function userIsAllowed($id, $userRole, $root, $errorMessage) {
        if ($userRole != $root) {
            if ($this->user->id != $id) {
                throw new ForbiddenRequestException($errorMessage);
            }
        }
    }

}
