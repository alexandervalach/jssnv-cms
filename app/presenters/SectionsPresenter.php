<?php

namespace App\Presenters;

use App\FormHelper;
use App\Forms\RemoveFormFactory;
use App\Forms\SectionFormFactory;
use App\Model\AlbumsRepository;
use App\Model\PostsRepository;
use App\Model\SectionsRepository;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;

/**
 * Class SectionsPresenter
 * @package App\Presenters
 */
class SectionsPresenter extends BasePresenter {

  /** @var ActiveRow */
  private $sectionRow;

  /** @var string */
  private $error = "Section not found!";

  /**
   * @var SectionFormFactory
   */
  private $sectionFormFactory;

  /**
   * @var PostsRepository
   */
  private $postsRepository;

  /**
   * @var RemoveFormFactory
   */
  private $removeFormFactory;

  /**
   * SectionsPresenter constructor.
   * @param AlbumsRepository $albumsRepository
   * @param SectionsRepository $sectionRepository
   * @param SectionFormFactory $sectionFormFactory
   * @param PostsRepository $postsRepository
   * @param RemoveFormFactory $removeFormFactory
   */
  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              SectionFormFactory $sectionFormFactory,
                              PostsRepository $postsRepository,
                              RemoveFormFactory $removeFormFactory)
  {
    parent::__construct($albumsRepository, $sectionRepository);
    $this->postsRepository = $postsRepository;
    $this->sectionFormFactory = $sectionFormFactory;
    $this->removeFormFactory = $removeFormFactory;
  }

  /**
   * @throws AbortException
   */
  public function actionAll() {
    $this->userIsLogged();
  }

  /**
   *
   */
  public function renderAll() {
    // Property $sections is inherited from parent
    $this->template->sections = $this->sections;
  }

  /**
   * @param $id
   * @throws BadRequestException
   */
  public function actionEdit($id) {
    $this->sectionRow = $this->sectionsRepository->findById($id);

    if (!$this->sectionRow || !$this->sectionRow->is_present) {
      throw new BadRequestException($this->error);
    }
  }

  /**
   * @param $id
   */
  public function renderEdit($id) {
    $this->template->mainSection = $this->sectionRow;
    $this['sectionForm']->setDefaults($this->sectionRow);
  }

  /**
   * @param $id
   * @throws BadRequestException
   * @throws AbortException
   */
  public function actionRemove($id) {
    $this->userIsLogged();
    $this->sectionRow = $this->sectionsRepository->findById($id);

    if (!$this->sectionRow) {
      throw new BadRequestException($this->error);
    }
  }

  /**
   * @param $id
   */
  public function renderRemove($id) {
    $this->template->mainSection = $this->sectionRow;
  }

  /**
   * @return Form
   */
  protected function createComponentSectionForm() {
    return $this->sectionFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->getParameter('id') ? $this->submittedEditForm($values) : $this->submittedAddForm($values);
    });
  }

  /**
   * @return Form
   */
  protected function createComponentRemoveForm() {
    return $this->removeFormFactory->create(function () {
      $this->userIsLogged();

      if (empty($this->sectionRow->url)) {
        $post = $this->sectionRow->related('posts')->fetch();
        $this->postsRepository->softDelete($post->id);
      }

      $sections = $this->sectionsRepository->findByParent($this->sectionRow->id);

      // Delete also children of given section
      foreach ($sections as $section) {
        $this->sectionsRepository->softDelete($section->id);
      }

      $this->sectionsRepository->softDelete($this->sectionRow->id);
      $this->flashMessage(self::ITEM_REMOVED);
      $this->redirect('all');
    }, function () {
      $this->redirect('all');
    });
  }

  /**
   * @param ArrayHash $values
   * @throws AbortException
   */
  private function submittedAddForm(ArrayHash $values) {
    $this->userIsLogged();

    $values->offsetSet('section_id', $values->section_id === 0 ? null : $values->section_id);
    $section = $this->sectionsRepository->insert($values);

    if (empty($values->url)) {
      $postData = array('section_id' => $section->id, 'name' => $values->name);
      $this->postsRepository->insert($postData);
      $this->flashMessage(self::ITEM_ADDED);
      $this->redirect('Posts:show', $section->id);
    } else {
      $this->flashMessage(self::ITEM_ADDED);
      $this->redirect('all');
    }
  }

  /**
   * @param ArrayHash $values
   * @throws AbortException
   */
  private function submittedEditForm(ArrayHash $values) {
    $this->userIsLogged();
    $values->section_id = $values->section_id === 0 ? null : $values->section_id;
    $this->sectionRow->update($values);
    $this->flashMessage(self::ITEM_UPDATED, self::SUCCESS);
    $this->redirect('all');
  }

}
