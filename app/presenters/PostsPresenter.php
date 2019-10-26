<?php

namespace App\Presenters;

use App\FormHelper;
use Nette\Database\Table\ActiveRow;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;

/**
 * Class PostsPresenter
 * @package App\Presenters
 */
class PostsPresenter extends BasePresenter {

  /** @var ActiveRow */
  private $postRow;

  /** @var ActiveRow */
  private $sectionRow;

  /** @var string */
  protected $storage = "files/";

  /** @var string */
  private $error = "Post not found!";

  /**
   * @param $id
   * @throws BadRequestException
   */
  public function actionShow ($id) {
    $this->postRow = $this->postsRepository->findByValue('section_id', $id)->fetch();

    if (!$this->postRow) {
      throw new BadRequestException($this->error);
    }

    $this->sectionRow = $this->postRow->ref('section_id');
    $this['editForm']->setDefaults($this->postRow);
  }

  /**
   *
   */
  public function renderShow () {
    $this->template->post = $this->postRow;
    $this->template->files = $this->filesRepository->findByValue('post_id', $this->postRow);
    $this->template->fileFolder = $this->storage;
  }

  /**
   * @param $id
   */
  public function actionEdit ($id) {

  }

  /**
   * @param sahl $id
   */
  public function renderEdit ($id) {

  }

  /**
   * @return Form
   */
  protected function createComponentEditForm() {
    $form = new Form;
    $form->addText('name', 'Názov');
    $form->addTextArea('content', 'Obsah:')
          ->setHtmlAttribute('id', 'ckeditor');
    $form->addCheckbox('onHomepage', ' Na domovskej stránke');
    $form->addSubmit('save', 'Uložiť');
    $form->addSubmit('cancel', 'Zrušiť')
          ->setHtmlAttribute('data-dismiss', 'modal')
          ->setHtmlAttribute('class', 'btn btn-large btn-warning');
    $form->onSuccess[] = [$this, 'submittedEditForm'];
    FormHelper::setBootstrapRenderer($form);
    return $form;
  }

  /**
   * @param Form $form
   * @param ArrayHash $values
   * @throws \Nette\Application\AbortException
   */
  public function submittedEditForm(Form $form, ArrayHash $values) {
    $this->userIsLogged();
    $this->postRow->update($values);

    if ($this->sectionRow->name != $values->name) {
      $this->sectionRow->update( array('name' => $values->name) );
    }

    $this->flashMessage('Položka bola upravená', self::SUCCESS);
    $this->redirect('show', $this->postRow->section_id);
  }

  /**
   * @throws \Nette\Application\AbortException
   */
  public function submittedRemoveForm() {
    $this->userIsLogged();
    $sectionRow = $this->postRow->ref('sections', 'section_id');
    $this->sectionsRepository->softDelete($sectionRow->id);
    $this->postsRepository->softDelete($this->postRow->id);
    $this->flashMessage('Sekcia bola odstránená.', 'alert-success');
    $this->redirect('Sections:all');
  }

  /**
   * @return Form
   */
  protected function createComponentUploadFilesForm() {
    $form = new Form;
    $form->addMultiUpload('files', 'Vyber súbory');
    $form->addSubmit('upload', 'Nahraj')
          ->onClick[] = [$this, 'submittedUploadFilesForm'];
    $form->addSubmit('cancel', 'Zrušiť')
          ->setHtmlAttribute('class', 'btn btn-warning')
          ->onClick[] = [$this, 'formCancelled'];
    FormHelper::setBootstrapRenderer($form);
    return $form;
  }

  /**
   * @param SubmitButton $btn
   * @param ArrayHash $values
   * @throws \Nette\Application\AbortException
   */
  public function submittedUploadFilesForm(SubmitButton $btn, ArrayHash $values) {
    $this->userIsLogged();
    $fileData = array();

    foreach ($values->files as $file) {
      $name = strtolower($file->getSanitizedName());

      if ($file->isOk()) {
        $file->move($this->storage . $name);

        $fileData['name'] = $name;
        $fileData['post_id'] = $this->postRow;
        $this->filesRepository->insert($fileData);
      }
    }

    $this->flashMessage('Súbory boli pridané.', 'alert-success');
    $this->redirect('show', $this->postRow);
  }

  /**
   * @throws \Nette\Application\AbortException
   */
  public function formCancelled() {
    $this->redirect('show', $this->postRow);
  }

}
