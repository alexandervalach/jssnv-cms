<?php

namespace App\Presenters;

use App\FormHelper;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;
use Nette\Forms\Controls\SubmitButton;

/**
 * Class AlbumPresenter
 * @package App\Presenters
 */
class AlbumsPresenter extends BasePresenter {

  /** @var ActiveRow */
  private $albumRow;

  /** @var string */
  private $error = "Album not found!";

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
   */
  public function actionEdit($id) {
    $this->userIsLogged();
    $this->albumRow = $this->albumsRepository->findById($id);

    if (!$this->albumRow) {
        throw new BadRequestException($this->error);
    }
  }

  /**
   * @param $id
   */
  public function renderEdit($id) {
    $this->template->mainAlbum = $this->albumRow;
    $this['editForm']->setDefaults($this->albumRow);
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
  protected function createComponentAddForm() {
    $form = new Form;
    $form->addText('name', 'Názov')
        ->setRequired('Názov musí byť vyplnený.')
        ->addRule(Form::MAX_LENGTH, 'Názov môže mať maximálne 50 znakov.', 50);

    $form->addSubmit('save', 'Zapísať');
    $form->onSuccess[] = [$this, 'submittedAddForm'];
    FormHelper::setBootstrapRenderer($form);
    return $form;
  }

  /**
   * @param Form $form
   * @param $values
   * @throws \Nette\Application\AbortException
   */
  public function submittedAddForm(Form $form, $values) {
    $this->userIsLogged();
    $id = $this->albumsRepository->insert($values);
    $this->flashMessage('Album bol pridaný');
    $this->redirect('Images:view', $id);
  }

  /**
   * @return Form
   */
  protected function createComponentEditForm() {
    $form = new Form;

    $form->addText('name', 'Názov')
      ->setRequired('Názov musí byť vyplnený.')
      ->addRule(Form::MAX_LENGTH, 'Názov môže mať maximálne 50 znakov.', 50);

    $form->addSubmit('save', 'Zapísať')
      ->onClick[] = [$this, 'submittedEditForm'];

    $form->addSubmit('cancel', 'Zrušiť')
      ->setHtmlAttribute('class', 'btn btn-warning')
      ->onClick[] = [$this, 'formCancelled'];

    FormHelper::setBootstrapRenderer($form);
    return $form;
  }

  /**
   * @param SubmitButton $btn
   * @throws \Nette\Application\AbortException
   */
  public function submittedEditForm(SubmitButton $btn) {
    $this->userIsLogged();
    $values = $btn->form->getValues();
    $this->albumRow->update($values);
    $this->flashMessage('Album bol upravený');
    $this->redirect('Images:view', $this->albumRow->id);
  }

  /**
   * @throws \Nette\Application\AbortException
   */
  public function formCancelled() {
    $this->redirect('Images:view#primary', $this->albumRow);
  }

}
