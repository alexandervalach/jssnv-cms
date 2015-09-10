<?php

namespace App\Presenters;

use App\FormHelper;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;

class SubSectionPresenter extends BasePresenter {

    /** @var ActiveRow */
    private $subSectionRow;

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
        $form->addText('link', 'Link')
                ->addRule(Form::MAX_LENGTH, 'Link môže mať maximálne 255 znakov', 255);
        $form->addSubmit('save', 'Zapísať');

        $form->onSuccess[] = $this->submittedAddForm;
        FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    public function submittedAddForm(Form $form) {
        $values = $form->getValues();
        $id = $this->subSectionRepository->insert($values);

        if (empty($values['link'])) {
            $postData = array(
                'subsection_id' => $id,
                'name' => $values['name']
            );
            $this->subPostRepository->insert($postData);
            $this->redirect('SubPost:show', $id);
        }
        $this->redirect('Homepage:');
    }

    protected function createComponentEditForm() {
        $form = new Form;
        $form->addText('name', 'Názov')
                ->setRequired('Názov musí byť vyplnený.')
                ->addRule(Form::MAX_LENGTH, 'Názov môže mať maximálne 50 znakov.', 50);
        $form->addText('link', 'Link')
                ->addRule(Form::MAX_LENGTH, 'Link môže mať maximálne 255 znakov', 255);
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

    public function submittedDeleteForm() {
        
    }

}
