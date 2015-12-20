<?php

namespace App\Presenters;

use App\FormHelper;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;

class SectionPresenter extends BasePresenter {

    /** @var ActiveRow */
    private $sectionRow;

    /** @var string */
    private $error = "Section not found!";

    public function actionAll() {
        $this->userIsLogged();
    }

    public function renderAll() {
        $this->template->sections = $this->sectionRepository->findAll()->order("order DESC");
    }

    public function actionEdit($id) {
        $this->sectionRow = $this->sectionRepository->findById($id);
    }

    public function renderEdit($id) {
        if (!$this->sectionRow) {
            throw new BadRequestException($this->error);
        }
        $this->template->section = $this->sectionRow;
        $this->getComponent('editForm')->setDefaults($this->sectionRow);
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
        $form->addText('order', 'Poradie')
                ->setDefaultValue(0)
                ->addRule(Form::INTEGER, 'Poradie môže byť len celé číslo.');
        $form->addCheckbox('sliding', ' Rolovacie menu');
        $form->addSubmit('save', 'Zapísať');

        $form->onSuccess[] = $this->submittedAddForm;
        FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    public function submittedAddForm(Form $form) {
        $values = $form->getValues();
        $id = $this->sectionRepository->insert($values);
        $postData = array(
            'section_id' => $id,
            'name' => $values['name']
        );
        $this->postRepository->insert($postData);
        $this->redirect('Post:show#primary', $id);
    }

    protected function createComponentEditForm() {
        $form = new Form;
        $form->addText('name', 'Názov')
                ->setRequired('Názov musí byť vyplnený.')
                ->addRule(Form::MAX_LENGTH, 'Názov môže mať maximálne 50 znakov.', 50);
        $form->addText('order', 'Poradie')
                ->addRule(Form::INTEGER, 'Poradie môže byť len celé číslo.');
        $form->addCheckbox('sliding', 'Rolovacie menu');
        $form->addSubmit('save', 'Zapísať');

        $form->onSuccess[] = $this->submittedEditForm;
        FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    public function submittedEditForm(Form $form) {
        $values = $form->getValues();
        $this->sectionRow->update($values);
        $this->redirect('Post:show#primary', $this->sectionRow->id);
    }

}
