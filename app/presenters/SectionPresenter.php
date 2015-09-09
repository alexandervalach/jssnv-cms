<?php

namespace App\Presenters;

use App\FormHelper;
use Nette\Application\UI\Form;

class SectionPresenter extends BasePresenter {

    public function actionEdit($id) {
        
    }

    public function renderEdit($id) {
        
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
        $form->addText('link', 'Link')
                ->addRule(Form::MAX_LENGTH, 'Link môže mať maximálne 255 znakov', 255);
        $form->addSubmit('save', 'Zapísať');

        $form->onSuccess[] = $this->submittedAddForm;
        FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    public function submittedAddForm(Form $form) {
        $values = $form->getValues();
        $id = $this->sectionRepository->insert($values);

        if ($values['link'] != '') {
            $postData = array(
                'section_id' => $id,
            );
            $this->postRepository->insert($postData);
            $this->redirect('Post:show', $id);
        }
        $this->redirect('Homepage:');
    }

    protected function createComponentEditForm() {
        
    }

    public function submittedEditForm(Form $form) {
        
    }

    public function submittedDeleteForm() {
        
    }

    public function formCancelled() {
        $this->redirect('Homepage:');
    }

}
