<?php

namespace App\Presenters;

use App\FormHelper;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;
use Nette\Forms\Controls\SubmitButton;

class AlbumPresenter extends BasePresenter {

    /** @var ActiveRow */
    private $albumRow;

    /** @var string */
    private $error = "Album not found!";

    public function actionAll() {
        
    }

    public function renderAll() {
        $this->template->listOfAlbums = $this->albumRepository->findAll();
        $this->template->imgFolder = $this->imgFolder;
    }

    public function actionEdit($id) {
        $this->userIsLogged();
        $this->albumRow = $this->albumRepository->findById($id);
    }

    public function renderEdit($id) {
        if (!$this->albumRow) {
            throw new BadRequestException($this->error);
        }
        $this->template->album = $this->albumRow;
        $this->getComponent('editForm')->setDefaults($this->albumRow);
    }

    public function actionAdd() {
        $this->userIsLogged();
    }

    public function renderAdd() {
        $this->getComponent('addForm');
    }

    protected function createComponentAddForm() {
        $form = new Form;
        $form->addText('name', 'Názov')
                ->setRequired('Názov musí byť vyplnený.')
                ->addRule(Form::MAX_LENGTH, 'Názov môže mať maximálne 50 znakov.', 50);
        $form->addSubmit('save', 'Zapísať');

        $form->onSuccess[] = $this->submittedAddForm;
        FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    public function submittedAddForm(Form $form) {
        $values = $form->getValues();
        $id = $this->albumRepository->insert($values);
        $this->redirect('Gallery:view', $id);
    }

    protected function createComponentEditForm() {
        $form = new Form;
        $form->addText('name', 'Názov')
                ->setRequired('Názov musí byť vyplnený.')
                ->addRule(Form::MAX_LENGTH, 'Názov môže mať maximálne 50 znakov.', 50);
        $form->addSubmit('save', 'Zapísať')
                ->onClick[] = $this->submittedEditForm;
        $form->addSubmit('cancel', 'Zrušiť')
                ->setAttribute('class', 'btn btn-warning')
                ->onClick[] = $this->formCancelled;

        $form->onSuccess[] = $this->submittedEditForm;
        FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    public function submittedEditForm(SubmitButton $btn) {
        $values = $btn->form->getValues();
        $this->albumRow->update($values);
        $this->redirect('Gallery:view', $this->albumRow->id);
    }

    public function formCancelled() {
        $this->redirect('Gallery:view#primary', $this->albumRow);
    }

}
