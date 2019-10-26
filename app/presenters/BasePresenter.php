<?php

namespace App\Presenters;

use App\Components\ModalDialog;
use App\Components\FormCancelled;
use App\Model\AlbumsRepository;
use App\Model\AnswersRepository;
use App\Model\SlidesRepository;
use App\Model\FilesRepository;
use App\Model\ImagesRepository;
use App\Model\PostImagesRepository;
use App\Model\LevelsResultsRepository;
use App\Model\LevelsRepository;
use App\Model\NoticesRepository;
use App\Model\PostsRepository;
use App\Model\QuestionsRepository;
use App\Model\ResultsRepository;
use App\Model\SectionsRepository;
use App\Model\SubFilesRepository;
use App\Model\SubPostRepository;
use App\Model\SubSectionRepository;
use App\Model\TestsRepository;
use App\Model\TestsResultsRepository;
use App\Model\UsersRepository;
use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\Presenter;
use Nette\Application\UI\Form;
use App\FormHelper;
use Nette\Utils\ArrayHash;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Presenter {

  const TEST_NOT_FOUND = 'Test not found';
  const ITEM_NOT_FOUND = 'Item not found';
  const ITEM_ADD_SUCCESS = 'Položka bola pridaná';
  const ITEM_EDIT_SUCCESS = 'Položka bola upravená';
  const SUCCESS = 'success';

  /** @var AlbumsRepository */
  protected $albumsRepository;

  /** @var AnswersRepository */
  protected $answersRepository;

  /** @var SlidesRepository */
  protected $slidesRepository;

  /** @var FilesRepository */
  protected $filesRepository;

  /** @var PostImagesRepository */
  protected $postImagesRepository;

  /** @var PostImagesRepository */
  protected $imagesRepository;

  /** @var LevelsResultsRepository */
  protected $levelsResultsRepository;

  /** @var LevelsRepository */
  protected $levelsRepository;

  /** @var NoticesRepository */
  protected $noticesRepository;

  /** @var PostsRepository */
  protected $postsRepository;

  /** @var QuestionsRepository */
  protected $questionsRepository;

  /** @var ResultsRepository */
  protected $resultsRepository;

  /** @var SectionsRepository */
  protected $sectionsRepository;

  /** @var TestsRepository */
  protected $testsRepository;

  /** @var UsersRepository */
  protected $usersRepository;

  /** @var ArrayHash */
  protected $sections;

  /** @var string */
  protected $imgFolder = "images/";

  /** @var string */
  protected $fileFolder = "files/";

  /**
   * BasePresenter constructor.
   * @param AlbumsRepository $albumsRepository
   * @param AnswersRepository $answersRepository
   * @param SlidesRepository $slidesRepository
   * @param FilesRepository $filesRepository
   * @param ImagesRepository $imagesRepository
   * @param LevelsResultsRepository $levelsResultsRepository
   * @param LevelsRepository $levelsRepository
   * @param NoticesRepository $noticesRepository
   * @param PostImagesRepository $postImagesRepository
   * @param PostsRepository $postsRepository
   * @param QuestionsRepository $questionsRepository
   * @param ResultsRepository $resultsRepository
   * @param SectionsRepository $sectionRepository
   * @param TestsRepository $testsRepository
   * @param UsersRepository $usersRepository
   */
  public function __construct(AlbumsRepository $albumsRepository,
                              AnswersRepository $answersRepository,
                              SlidesRepository $slidesRepository,
                              FilesRepository $filesRepository,
                              ImagesRepository $imagesRepository,
                              LevelsResultsRepository $levelsResultsRepository,
                              LevelsRepository $levelsRepository,
                              NoticesRepository $noticesRepository,
                              PostImagesRepository $postImagesRepository,
                              PostsRepository $postsRepository,
                              QuestionsRepository $questionsRepository,
                              ResultsRepository $resultsRepository,
                              SectionsRepository $sectionRepository,
                              TestsRepository $testsRepository,
                              UsersRepository $usersRepository) {
    parent::__construct();
    $this->albumsRepository = $albumsRepository;
    $this->answersRepository = $answersRepository;
    $this->slidesRepository = $slidesRepository;
    $this->filesRepository = $filesRepository;
    $this->imagesRepository = $imagesRepository;
    $this->levelsResultsRepository = $levelsResultsRepository;
    $this->levelsRepository = $levelsRepository;
    $this->postImagesRepository = $postImagesRepository;
    $this->postsRepository = $postsRepository;
    $this->questionsRepository = $questionsRepository;
    $this->resultsRepository = $resultsRepository;
    $this->sectionsRepository = $sectionRepository;
    $this->noticesRepository = $noticesRepository;
    $this->testsRepository = $testsRepository;
    $this->usersRepository = $usersRepository;
  }

  /**
   *
   */
  public function beforeRender() {
    $sections = $this->sectionsRepository->findByParent(null);
    $items = [];

    foreach ($sections as $section) {
      $items[$section->id]['subsections'] = $this->sectionsRepository->findByParent($section->id);
      $items[$section->id]['name'] = $section->name;
      $items[$section->id]['url'] = $section->url;
      $items[$section->id]['order'] = $section->order;
      $items[$section->id]['sliding'] = $section->sliding;
      $items[$section->id]['homeUrl'] = $section->homeUrl;
    }

    $this->sections = ArrayHash::from($items);

    $this->template->menuSections = $this->sections;
    $this->template->menuAlbums = $this->albumsRepository->findAll();
    $this->template->imgFolder = $this->imgFolder;
  }

  /**
   * @throws \Nette\Application\AbortException
   */
  protected function userIsLogged() {
    if (!$this->user->isLoggedIn()) {
      $this->redirect('Sign:in');
    }
  }

  /**
   * @return ModalDialog
   */
  protected function createComponentModalDialog() {
    return new ModalDialog();
  }

  /**
   * @return FormCancelled
   */
  protected function createComponentFormCancelled() {
    return new FormCancelled();
  }

  /**
   * @return Form
   */
  protected function createComponentRemoveForm() {
    $form = new Form();
    $form->addSubmit('remove', 'Odstrániť')
        ->setHtmlAttribute('class', 'btn btn-danger');
    $form->addSubmit('cancel', 'Zrušiť')
        ->setHtmlAttribute('class', 'btn btn-warning')
        ->setHtmlAttribute('data-dismiss', 'modal');
    $form->onSuccess[] = [$this, 'submittedRemoveForm'];
    FormHelper::setBootstrapRenderer($form);
    return $form;
  }

  /**
   * @param $id
   * @param $userRole
   * @param $root
   * @param $errorMessage
   * @throws ForbiddenRequestException
   */
  protected function userIsAllowed($id, $userRole, $root, $errorMessage) {
    if ($userRole != $root) {
      if ($this->user->id != $id) {
          throw new ForbiddenRequestException($errorMessage);
      }
    }
  }

}
