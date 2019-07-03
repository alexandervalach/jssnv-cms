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
    $this->questions = $this->questionsRepository->findQuestions($this->testRow);
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

    if (isset($postData['url']) && !empty($postData['url'])) {
      $this->redirect('Homepage:');
    }

  	$resultId = $this->evaluateTest($postData);
    $this->redirect('Results:view', $resultId);
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
    $form->addText('email', 'E-mail')
      ->addCondition(\Nette\Application\UI\Form::EMAIL, true);
    $form->addText('url', 'Mňam, mňa, toto vyplní len robot')
      ->setAttribute('style', 'opacity: 0; display: inline')
      ->setDefaultValue('');
    $form->addSubmit('finish', 'Ukončiť test');
    $form->onSuccess[] = [$this, 'submittedFinishForm'];

    \App\FormHelper::setBootstrapRenderer($form);
    return $form;
  }

  protected function evaluateTest ($postData) {
    $earnedPoints = array();
    $levels = array();
    $highScore = 0;
    $score = 0;

  	foreach ($this->questions as $question) {
      if (!isset($levels[$question->level_id])) {
        $levels[$question->level_id]['score'] = 0;
        $levels[$question->level_id]['high_score'] = 0;
        $levels[$question->level_id]['level_id'] = $question->level_id;
      }

      $levels[$question->level_id]['high_score'] += $question->value;
  		$answer[$question->id] = $question->related('answers')->where('correct', 1)->fetch();
  		$highScore += $question->value;
  	}

  	foreach ($this->questions as $question) {
      if (!(array_key_exists('question' . $question->id, $postData))) {
        continue;
      }

      if ((float) $postData['question' . $question->id] === (float) $answer[$question->id]->id) {
        $levels[$question->level_id]['score'] += $question->value;
        $score += $question->value;
  		}
    }

    $email = array_key_exists('email', $postData) ? $postData['email'] : 'anonym';

    $resultId = $this->resultsRepository->insert(
      array(
        'test_id' => $this->testRow->id,
        'score' => round(($score / (float) $highScore) * 100, 2),
        'email' => $email
      )
    );

    foreach ($levels as $level) {
      $this->levelsResultsRepository->insert(
        array(
          'result_id' => $resultId,
          'level_id' => $level['level_id'],
          'score' => round($level['score'] / (float) $levels[$question->level_id]['high_score'] * 100, 2)
        )
      );
    }

    return $resultId;
  }
}
