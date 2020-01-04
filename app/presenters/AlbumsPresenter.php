<?php

namespace App\Presenters;

use App\Helpers\FormHelper;
use App\Model\AlbumsRepository;
use App\Model\SectionsRepository;
use App\Forms\AlbumFormFactory;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;

/**
 * Class AlbumPresenter
 * @package App\Presenters
 */
class AlbumsPresenter extends BasePresenter {

  /** @var ActiveRow */
  private $albumRow;

  /** @var string */
  private $error = "Album not found!";

  /** @var AlbumFormFactory */
  private $albumFormFactory;

  public function __construct(AlbumsRepository $albumsRepository, SectionsRepository $sectionRepository, AlbumFormFactory $albumFormFactory)
  {
    parent::__construct($albumsRepository, $sectionRepository);
    $this->albumFormFactory = $albumFormFactory;
  }

  /**
   *
   */
  public function renderAll() {
    $this->template->listOfAlbums = $this->albumsRepository->findAll();
    $this->template->imgFolder = $this->imgFolder;
  }

  /**
   * @param $id
   * @throws BadRequestException
   * @throws \Nette\Application\AbortException
   */
  public function actionView(int $id) {
    $this->userIsLogged();
    $this->albumRow = $this->albumsRepository->findById($id);

    if (!$this->albumRow) {
      throw new BadRequestException(self::ITEM_NOT_FOUND);
    }
  }

  /**
   * @param $id
   */
  public function renderView(int $id) {
    $this->template->mainAlbum = $this->albumRow;
    $this['albumForm']->setDefaults($this->albumRow);
  }

  /**
   *
   */
  public function actionAdd() {
    $this->userIsLogged();
  }

  /**
   * @return Form
   */
  protected function createComponentAlbumForm() {
    return $this->albumFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->userIsLogged();

      $id = $this->getParameter('id');

      if ($id) {
        $this->submittedEditForm($values);
      } else {
        $this->submittedAddForm($values);
      }
    });
  }

  /**
   * @param Form $form
   * @param $values
   * @throws \Nette\Application\AbortException
   */
  private function submittedAddForm(ArrayHash $values) {
    $album = $this->albumsRepository->insert($values);
    $this->flashMessage(self::ITEM_ADDED);
    $this->redirect('Albums:view', $album->id);
  }

  /**
   * @param int $id
   * @param ArrayHash $values
   * @throws \Nette\Application\AbortException
   */
  public function submittedEditForm(ArrayHash $values) {
    $this->albumRow->update($values);
    $this->flashMessage('Album bol upravený');
    $this->redirect('Albums:view', $this->albumRow->id);
  }

}
