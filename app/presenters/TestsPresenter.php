<?php

namespace App\Presenters;

use App\FormHelper;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;

namespace App\Presenters;

class TestsPresenter extends BasePresenter {

    /** @var ActiveRow */
    private $testRow;

    public function actionAll () {
    	if (!$this->user->isLoggedIn()) {
    		$this->redirect('Homepage:');
    	}
    }

    public function renderAll () {
    	$this->template->tests = $this->testsRepository->findAll();
    }

    public function actionView ($id) {
        $this->testRow = $this->testsRepository->findById($id);
        
        if (!$this->testRow) {
            $this->error(self::TEST_NOT_FOUND);
        }
    }

    public function renderView ($id) {
        $this->template->test = $this->testRow;
        $this->template->questions = $this->testRow->related(self::QUESTIONS);
    }

    public function actionEdit ($id) {
        $this->userIsLogged();
        $this->testRow = $this->testsRepository->findById($id);
        
        if (!$this->testRow) {
            $this->error(self::TEST_NOT_FOUND);
        }
        
        $this['editForm']->setDefaults($this->testRow);
    }

    public function renderEdit ($id) {
        $this->template->test = $this->testsRepository->findById($id);
    }

    public function submittedAddForm ($form, $values) {
        $this->testsRepository->insert($values);
        $this->flashMessage(self::ITEM_ADD_SUCCESS);
        $this->redirect('all'); 
    }

    public function submittedEditForm ($form, $values) {
        $this->testRow->update($values);
        $this->flashMessage(self::ITEM_EDIT_SUCCESS);
        $this->redirect('all'); 
    }

    public function submittedFinishForm ($form, $values) {
        $id = $this->resultsRepository->insert($values);
        $this->redirect('Results:view', $id);
    }

    protected function createComponentAddForm () {
        $form = new \Nette\Application\UI\Form;
        $form->addText('label', 'Názov');
        $form->addSubmit('save', 'Uložiť');
        $form->onSuccess[] = [$this, 'submittedAddForm'];

        \App\FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    protected function createComponentEditForm () {
        $form = new \Nette\Application\UI\Form;
        $form->addText('label', 'Názov');
        $form->addSubmit('save', 'Uložiť');
        $form->onSuccess[] = [$this, 'submittedEditForm'];

        \App\FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    protected function createComponentFinishForm () {
        $form = new \Nette\Application\UI\Form;
        $form->addText('label', 'E-mail');
        $form->addSubmit('save', 'Ukončiť test');
        $form->onSuccess[] = [$this, 'submittedFinishForm'];

        \App\FormHelper::setBootstrapRenderer($form);
        return $form;
    }

}
