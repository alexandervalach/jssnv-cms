<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\BreadcrumbControl;
use App\Model\AlbumsRepository;
use App\Model\SectionsRepository;
use Nette\Application\AbortException;
use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\Presenter;
use Nette\Utils\ArrayHash;

/**
 * Base presenter for all application Presenters.
 */
abstract class BasePresenter extends Presenter
{
  const TEST_NOT_FOUND = 'Test not found!';
  const ITEM_NOT_FOUND = 'Item not found!';
  const FORBIDDEN = 'Action not allowed!';
  const ITEM_ADDED = 'Položka bola pridaná.';
  const ITEM_UPDATED = 'Položka bola upravená.';
  const ITEM_REMOVED = 'Položka bola odstránená.';
  const ITEMS_ADDED = 'Položky boli nahraté.';
  const USER_ADDED = 'Používateľ bol pridaný.';
  const FILE_NOT_FOUND = 'Súbor nebol nájdený.';
  const UPLOAD_ERROR = 'Nastala chyba pri nahrávaní súboru.';
  const SUCCESS = 'success';
  const ERROR = 'danger';
  const INFO = 'info';
  const IMAGE_FOLDER = 'images';
  const FILE_FOLDER = 'files';

  /** @var AlbumsRepository */
  protected $albumsRepository;

  /** @var SectionsRepository */
  protected $sectionsRepository;

  /** @var ArrayHash */
  protected $sections;

  /**
   * @var BreadcrumbControl
   */
  private $breadcrumbControl;

  /**
   * BasePresenter constructor.
   * @param AlbumsRepository $albumsRepository
   * @param SectionsRepository $sectionRepository
   * @param BreadcrumbControl $breadcrumbControl
   */
  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              BreadcrumbControl $breadcrumbControl)
  {
    parent::__construct();
    $this->albumsRepository = $albumsRepository;
    $this->sectionsRepository = $sectionRepository;
    $this->breadcrumbControl = $breadcrumbControl;
  }

  /**
   *
   */
  public function beforeRender()
  {
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
    $this->template->imgFolder = self::IMAGE_FOLDER;
    $this->template->fileFolder = self::FILE_FOLDER;
  }

  /**
   * @throws AbortException
   */
  protected function userIsLogged()
  {
    if (!$this->user->isLoggedIn()) {
      $this->redirect('Sign:in');
    }
  }

  /**
   * @param $id
   * @param $currentUserRole
   * @param $privilegedUserRole
   * @throws ForbiddenRequestException
   */
  protected function userIsAllowed($id, $currentUserRole, $privilegedUserRole)
  {
    if ($currentUserRole != $privilegedUserRole) {
      if ($this->user->id != $id) {
        throw new ForbiddenRequestException(self::FORBIDDEN);
      }
    }
  }

  protected function createComponentBreadcrumb()
  {
    return new BreadcrumbControl();
  }

}
