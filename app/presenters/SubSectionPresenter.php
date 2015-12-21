<?php

namespace App\Presenters;

use App\FormHelper;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;

class SubSectionPresenter extends BasePresenter {

    /** @var ActiveRow */
    private $subSectionRow;

    public function actionAll() {
        $this->userIsLogged();
    }

    public function renderAll() {
        $this->template->listOfSubSections = $this->subSectionRepository->findAll()->order('section_id DESC');
    }

    public function actionEdit($id) {
        $this->subSectionRow = $this->subSectionRepository->findById($id);
    }

    public function renderEdit($id) {
        if (!$this->subSectionRow) {
            throw new BadRequestException($this->error);
        }
        $this->template->subSection = $this->subSectionRow;
        $this->getComponent('editForm')->setDefaults($this->subSectionRow);
    }

    public function actionAdd() {
        $this->userIsLogged();
    }

    public function renderAdd() {
        $this->getComponent('addForm');
    }

    protected function createComponentAddForm() {
        $form = new Form;
        $sections = $this->sectionRepository->getSections();
        $form->addSelect('section_id', 'Vyberte sekciu', $sections);
        $form->addText('name', 'Názov')
                ->setRequired('Názov musí byť vyplnený.')
                ->addRule(Form::MAX_LENGTH, 'Názov môže mať maximálne 50 znakov.', 50);
        $form->addText('url', 'URL adresa')
                ->addRule(Form::MAX_LENGTH, 'URL môže mať maximálne 200 znakov.', 200);
        $form->addText('order', 'Poradie')
                ->setDefaultValue(5)
                ->addRule(Form::INTEGER, 'Poradie môže byť len celé číslo.');
        $form->addCheckbox('visible', ' Viditeľné v bočnom menu')
                ->setDefaultValue(1);
        $form->addSubmit('save', 'Zapísať');

        $form->onSuccess[] = $this->submittedAddForm;
        FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    public function submittedAddForm(Form $form) {
        $values = $form->getValues();
        $id = $this->subSectionRepository->insert($values);
        $postData = array(
            'subsection_id' => $id,
            'name' => $values['name']
        );
        $this->subPostRepository->insert($postData);
        $this->redirect('SubPost:show', $id);
    }

    protected function createComponentEditForm() {
        $form = new Form;
        $form->addText('name', 'Názov')
                ->setRequired('Názov musí byť vyplnený.')
                ->addRule(Form::MAX_LENGTH, 'Názov môže mať maximálne 50 znakov.', 50);
        $form->addText('url', 'URL adresa')
                ->addRule(Form::MAX_LENGTH, 'URL môže mať maximálne 200 znakov.', 200);
        $form->addText('order', 'Poradie')
                ->setDefaultValue(5)
                ->addRule(Form::INTEGER, 'Poradie môže byť len celé číslo.');
        $form->addCheckbox('visible', ' Viditeľné v bočnom menu')
                ->setDefaultValue(1);
        $form->addSubmit('save', 'Zapísať');

        $form->onSuccess[] = $this->submittedEditForm;
        FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    public function submittedEditForm(Form $form) {
        $values = $form->getValues();
        $this->subSectionRow->update($values);
        $this->redirect('SubPost:show', $this->subSectionRow->id);
    }

}
