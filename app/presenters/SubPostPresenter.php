<?php

namespace App\Presenters;

use App\FormHelper;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;

class SubPostPresenter extends BasePresenter {

  /** @var ActiveRow */
  private $subPostRow;

  /** @var ActiveRow */
  private $subSectionRow;

  /** @var string */
  private $storage = 'files/';

  /** @var string */
  private $error = "Post not found!";

  public function actionShow($id) {
    $this->subPostRow = $this->subPostRepository->findByValue('subsection_id', $id)->fetch();
    $this->subSectionRow = $this->subPostRow->ref('subsection_id');

    if (!$this->subPostRow) {
      throw new BadRequestException($this->error);
    }

    $this['editForm']->setDefaults($this->subPostRow);
  }

  public function renderShow($id) {
    $this->template->subPost = $this->subPostRow;
    $this->template->files = $this->subFilesRepository->findByValue('subpost_id', $this->subPostRow);
    $this->template->fileFolder = $this->storage;
  }

  protected function createComponentEditForm() {
    $form = new Form;
    $form->addText('name', 'Názov');
    $form->addTextArea('content', 'Obsah')
          ->setAttribute('id', 'ckeditor');
    $form->addCheckbox('onHomepage', ' Na domovskej stránke');
    $form->addSubmit('save', 'Uložiť');
    $form->addSubmit('cancel', 'Zrušiť')
          ->setAttribute('class', 'btn btn-warning')
          ->setAttribute('data-dismiss', 'modal');
    $form->onSuccess[] = [$this, 'submittedEditForm'];
    FormHelper::setBootstrapRenderer($form);
    return $form;
  }

  public function submittedEditForm(Form $form, ArrayHash $values) {
    $this->userIsLogged();
    if ($this->subSectionRow->name != $values->name) {
      $this->subSectionRow->update( array('name' => $values->name) );
    }
    $this->subPostRow->update($values);

    $this->flashMessage('Položka bola upravená', self::SUCCESS);
    $this->redirect('show', $this->subPostRow->subsection_id);
  }

  public function submittedRemoveForm(Form $form) {
    $subSectionRow = $this->subPostRow->ref('subsection', 'subsection_id');
    $subSectionRow->delete();
    $this->subPostRow->delete();
    $this->flashMessage('Sekcia bola odstránená.', 'alert-success');
    $this->redirect('Section:all');
  }

  protected function createComponentUploadFilesForm() {
    $form = new Form;
    $form->addUpload('files', 'Vyber súbory', true);
    $form->addSubmit('upload', 'Nahraj')
          ->onClick[] = [$this, 'submittedUploadFilesForm'];
    $form->addSubmit('cancel', 'Zrušiť')
          ->setAttribute('class', 'btn btn-warning')
          ->onClick[] = [$this, 'formCancelled'];
    FormHelper::setBootstrapRenderer($form);
    return $form;
  }

  public function submittedUploadFilesForm(SubmitButton $btn) {
    $this->userIsLogged();

    $values = $btn->form->getValues();
    $fileData = array();
    foreach ($values['files'] as $file) {
      $name = strtolower($file->getSanitizedName());

      if ($file->isOk()) {
        $file->move($this->storage . $name);

        $fileData['name'] = $name;
        $fileData['subpost_id'] = $this->subPostRow;
        $this->subFilesRepository->insert($fileData);
      }
    }
    $this->redirect('show', $this->subPostRow);
  }

  public function formCancelled() {
    $this->redirect('show', $this->subPostRow);
  }
}
