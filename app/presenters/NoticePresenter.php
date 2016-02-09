<?php

namespace App\Presenters;

use App\FormHelper;
use App\Model\NoticeRepository;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;
use Nette\Forms\Controls\SubmitButton;
use Nette\Application\BadRequestException;

class NoticePresenter extends BasePresenter {

    /** @var ActiveRow */
    private $noticeRow;

    /** @var string */
    private $error = "Notice not found!";

    public function actionAll() {
        
    }

    public function renderAll() {
        $this->template->notices = $this->noticeRepository->findAll()->order('time DESC');
    }

    public function actionAdd() {
        $this->userIsLogged();
    }

    public function renderAdd() {
        $this->getComponent('addForm');
    }

    public function actionEdit($id) {
        $this->userIsLogged();
        $this->noticeRow = $this->noticeRepository->findById($id);
    }

    public function renderEdit($id) {
        $this->template->notice = $this->noticeRow;
        $this->getComponent('editForm')->setDefaults($this->noticeRow);
    }

    public function actionRemove($id) {
        $this->userIsLogged();
        $this->noticeRow = $this->noticeRepository->findById($id);
    }

    public function renderRemove($id) {
        if (!$this->noticeRow) {
            throw new BadRequestException($this->error);
        }
        $this->template->notice = $this->noticeRow;
        $this->getComponent('removeForm');
    }

    protected function createComponentAddForm() {
        $form = new Form;
        $form->addSelect('type', 'Typ', NoticeRepository::$flag);
        $form->addText('name', 'Názov')
                ->setRequired("Názov musí byť vyplnený");
        $form->addTextArea('content', 'Text')
                ->setAttribute('id', 'ckeditor');
        $form->addSubmit('save', 'Zapísať');
        $form->onSuccess[] = $this->submittedAddForm;
        FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    protected function createComponentEditForm() {
        $form = new Form;
        $form->addSelect('type', 'Typ', NoticeRepository::$flag);
        $form->addText('name', 'Názov')
                ->setRequired("Názov musí byť vyplnený");
        $form->addTextArea('content', 'Text')
                ->setAttribute('id', 'ckeditor');
        $form->addSubmit('save', 'Zapísať')
                ->onClick[] = $this->submittedEditForm;
        $form->addSubmit('cancel', 'Zrušiť')
                        ->setAttribute('class', 'btn btn-warning')
                ->onClick[] = $this->formCancelled;
        FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    protected function createComponentRemoveForm() {
        $form = new Form;
        $form->addSubmit('cancel', 'Zrušiť')
                        ->setAttribute('class', 'btn btn-warning')
                ->onClick[] = $this->formCancelled;
        $form->addSubmit('remove', 'Odstrániť')
                        ->setAttribute('class', 'btn btn-danger')
                ->onClick[] = $this->submittedRemoveForm;
        return $form;
    }

    public function submittedAddForm(Form $form) {
        $values = $form->getValues();
        $values['time'] = date('Y-m-d h:i:s');
        $this->noticeRepository->insert($values);
        $this->redirect('all#primary');
    }

    public function submittedEditForm(SubmitButton $btn) {
        $values = $btn->form->getValues();
        $this->noticeRow->update($values);
        $this->redirect('all#primary');
    }

    public function submittedRemoveForm() {
        $this->noticeRow->delete();
        $this->redirect('all#primary');
    }

    public function formCancelled() {
        $this->redirect('all#primary');
    }

}
