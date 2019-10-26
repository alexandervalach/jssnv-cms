<?php

namespace App\Presenters;

use App\FormHelper;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Utils\FileSystem;

/**
 * Class FilesPresenter
 * @package App\Presenters
 */
class FilesPresenter extends BasePresenter {

  /** @var ActiveRow */
  private $fileRow;

  /** @var string */
  private $error = "File not found!";

  /**
   * @throws \Nette\Application\AbortException
   */
  public function actionAll() {
    $this->userIsLogged();
  }

  /**
   *
   */
  public function renderAll() {
    $this->template->files = $this->filesRepository->findAll()->order('name ASC');
    $this->template->fileFolder = $this->fileFolder;
  }

  /**
   * @param $id
   * @throws BadRequestException
   * @throws \Nette\Application\AbortException
   */
  public function actionRemove($id) {
    $this->userIsLogged();
    $this->fileRow = $this->filesRepository->findById($id);

    if (!$this->fileRow) {
      throw new BadRequestException($this->error);
    }
  }

  /**
   * @param $id
   */
  public function renderRemove($id) {
    $this->template->file = $this->fileRow;
  }

  /**
   * @return Form
   */
  protected function createComponentRemoveFileForm() {
    $form = new Form;

    $form->addSubmit('cancel', 'Zrušiť')
        ->setHtmlAttribute('class', 'btn btn-warning')
        ->onClick[] = [$this, 'formCancelled'];

    $form->addSubmit('remove', 'Odstrániť')
        ->setHtmlAttribute('class', 'btn btn-danger')
        ->onClick[] = [$this, 'submittedFileRemoveForm'];

    FormHelper::setBootstrapRenderer($form);
    return $form;
  }

  /**
   * @throws \Nette\Application\AbortException
   */
  public function formCancelled() {
    $this->redirect('Posts:show#primary', $this->fileRow->ref('posts', 'post_id'));
  }

  /**
   * @throws \Nette\Application\AbortException
   */
  public function submittedFileRemoveForm() {
    $this->userIsLogged();
    $id = $this->fileRow->ref('posts', 'post_id');
    $this->filesRepository->softDelete($this->fileRow->id);
    $this->redirect('Posts:show#primary', $id);
  }

}
