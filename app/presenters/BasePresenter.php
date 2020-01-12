<?php

namespace App\Presenters;

use App\Components\ModalDialog;
use App\Components\FormCancelled;
use App\Model\AlbumsRepository;
use App\Model\SectionsRepository;
use Nette\Application\AbortException;
use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\Presenter;
use Nette\Utils\ArrayHash;

/**
 * Base presenter for all application Presenters.
 */
abstract class BasePresenter extends Presenter {

  const TEST_NOT_FOUND = 'Test not found';
  const ITEM_NOT_FOUND = 'Item not found';
  const ITEM_ADDED = 'Položka bola pridaná';
  const ITEM_UPDATED = 'Položka bola upravená';
  const ITEM_REMOVED = 'Položka bola odstránená';
  const ITEMS_ADDED = 'Položky boli nahraté';
  const SUCCESS = 'success';

  /** @var AlbumsRepository */
  protected $albumsRepository;

  /** @var SectionsRepository */
  protected $sectionsRepository;

  /** @var ArrayHash */
  protected $sections;

  /** @var string */
  protected $imgFolder = 'images';

  /** @var string */
  protected $fileFolder = 'files';

  /**
   * BasePresenter constructor.
   * @param AlbumsRepository $albumsRepository
   * @param SectionsRepository $sectionRepository
   */
  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository) {
    parent::__construct();
    $this->albumsRepository = $albumsRepository;
    $this->sectionsRepository = $sectionRepository;
  }

  /**
   *
   */
  public function beforeRender() {
    $sections = $this->sectionsRepository->findByParent(null);
    $items = [];

    foreach ($sections as $section) {
      $items[$section->id]['subsections'] = $this->sectionsRepository->findByParent($section->id);
      $items[$section->id]['id'] = $section->id;
      $items[$section->id]['name'] = $section->name;
      $items[$section->id]['url'] = $section->url;
      $items[$section->id]['order'] = $section->order;
      $items[$section->id]['sliding'] = $section->sliding;
      $items[$section->id]['visible'] = $section->visible;
      $items[$section->id]['on_homepage'] = $section->on_homepage;
      $items[$section->id]['home_url'] = $section->home_url;
    }

    $this->sections = ArrayHash::from($items);

    $this->template->menuSections = $this->sections;
    $this->template->menuAlbums = $this->albumsRepository->findAll();
    $this->template->imgFolder = $this->imgFolder;
    $this->template->fileFolder = $this->fileFolder;
  }

  /**
   * @throws AbortException
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
