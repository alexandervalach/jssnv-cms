<?php

namespace App\Presenters;

use App\FormHelper;
use Nette\Application\UI\Form;

class NoticePresenter extends BasePresenter {

    public function actionAdd() {
        
    }

    public function renderAdd() {
        
    }

    public function actionEdit() {
        
    }

    public function renderEdit() {
        
    }

    protected function createComponentAddForm() {
        FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    protected function createComponentEditForm() {
        
    }

    public function submittedAddForm(Form $form) {
        
    }

    public function submittedEditForm(Form $form) {
        
    }

    public function submittedDeleteForm() {
        
    }

}
