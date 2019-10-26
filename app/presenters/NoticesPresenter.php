<?php

namespace App\Presenters;

use App\FormHelper;
use App\Model\NoticesRepository;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;
use Nette\Forms\Controls\SubmitButton;
use Nette\Application\BadRequestException;

class NoticesPresenter extends BasePresenter {

  /** @var ActiveRow */
  private $noticeRow;

  /** @var string */
  private $error = "Notice not found";

  public function renderAll() {
    $this->template->notices = $this->noticesRepository->getAll();
  }

  public function actionEdit($id) {
    $this->userIsLogged();
    $this->noticeRow = $this->noticesRepository->findById($id);

    if (!$this->noticeRow) {
      throw new BadRequestException($this->error);
    }

    $this->getComponent('editForm')->setDefaults($this->noticeRow);
  }

  public function renderEdit($id) {
    $this->template->notice = $this->noticeRow;
  }

  public function actionRemove($id) {
    $this->userIsLogged();
    $this->noticeRow = $this->noticesRepository->findById($id);

    if (!$this->noticeRow) {
      throw new BadRequestException($this->error);
    }
  }

  public function renderRemove($id) {
    $this->template->notice = $this->noticeRow;
  }

  protected function createComponentAddForm() {
    $form = new Form;
    $form->addSelect('type', 'Paleta', NoticesRepository::$flag);
    $form->addText('name', 'Názov')
            ->setRequired("Názov musí byť vyplnený");
    $form->addTextArea('content', 'Text')
            ->setHtmlAttribute('id', 'ckeditor');
    $form->addSubmit('save', 'Uložiť');
    $form->addSubmit('cancel', 'Zrušiť')
          ->setHtmlAttribute('class', 'btn btn-large btn-warning')
          ->setHtmlAttribute('data-dismiss', 'modal');

    $form->onSuccess[] = [$this, 'submittedAddForm'];
    FormHelper::setBootstrapRenderer($form);
    return $form;
  }

  protected function createComponentEditForm() {
    $form = new Form;
    $form->addSelect('type', 'Paleta', NoticesRepository::$flag);
    $form->addText('name', 'Názov')
            ->setRequired("Názov musí byť vyplnený");
    $form->addTextArea('content', 'Text')
            ->setHtmlAttribute('id', 'ckeditor');
    $form->addSubmit('save', 'Uložiť')
            ->onClick[] = [$this, 'submittedEditForm'];
    $form->addSubmit('cancel', 'Zrušiť')
            ->setHtmlAttribute('class', 'btn btn-warning')
            ->onClick[] = [$this, 'formCancelled'];
    FormHelper::setBootstrapRenderer($form);
    return $form;
  }

  protected function createComponentRemoveForm() {
    $form = new Form;
    $form->addSubmit('cancel', 'Zrušiť')
          ->setHtmlAttribute('class', 'btn btn-warning')
          ->onClick[] = [$this, 'formCancelled'];
    $form->addSubmit('remove', 'Odstrániť')
          ->setHtmlAttribute('class', 'btn btn-danger')
          ->onClick[] = [$this, 'submittedRemoveForm'];
    FormHelper::setBootstrapRenderer($form);
    return $form;
  }

  public function submittedAddForm(Form $form, $values) {
    $this->userIsLogged();
    $this->noticesRepository->insert($values);
    $this->redirect('all#primary');
  }

  public function submittedEditForm(SubmitButton $btn) {
    $this->userIsLogged();
    $values = $btn->form->getValues();
    $this->noticeRow->update($values);
    $this->redirect('all#primary');
  }

  public function submittedRemoveForm() {
    $this->userIsLogged();
    $this->noticesRepository->softDelete($this->noticeRow->id);
    $this->redirect('all#primary');
  }

  public function formCancelled() {
    $this->redirect('all#primary');
  }

}
