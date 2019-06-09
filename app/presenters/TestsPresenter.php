<?php

namespace App\Presenters;

use App\FormHelper;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;

namespace App\Presenters;

class TestsPresenter extends BasePresenter {

  /** @var ActiveRow */
  private $testRow;

  /** @var Selection **/ 
  private $questions;

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

    $this['editForm']->setDefaults($this->testRow);
    $this->questions = $this->testRow->related(self::QUESTIONS);
  }

  public function renderView ($id) {
    $this->template->test = $this->testRow;
    $this->template->questions = $this->questions;
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
  	$postData = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

  	$score = $this->evaluateTest($postData);

    $data = array(
      'email' => 'alexander.valach@gmail.com',
      'score' => $score
    );

    $id = $this->resultsRepository->insert($data);
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
    $form->addText('email', 'E-mail');
    $form->addSubmit('finish', 'Ukončiť test');
    $form->addHidden('test_id', $this->testRow);
    $form->onSuccess[] = [$this, 'submittedFinishForm'];

    \App\FormHelper::setBootstrapRenderer($form);
    return $form;
  }

  protected function evaluateTest ($postData) {
  	$correctAnswers = 0;

  	foreach ($this->questions as $question) {
  		$answer[$question->id] = $question->related('answers')->where('correct', 1)->fetch();
  	}

  	foreach ($this->questions as $question) {
  		if ((int) $postData['question' . $question->id] === (int) $answer[$question->id]->id) {
  			$correctAnswers += $question->value;
  		}
  	}

  	return $correctAnswers;
  }
}
