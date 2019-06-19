<?php

namespace App\Presenters;

use App\FormHelper;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;

namespace App\Presenters;

class AnswersPresenter extends BasePresenter {

  /** @var ActiveRow */
  private $answerRow;

  /** @var ActiveRow **/ 
  private $questionRow;
  
  /** @var ActiveRow */
  private $testRow;

  /**
   * @param id test id 
   */
  public function actionAll ($id) {
    $this->userIsLogged();
    $this->questionRow = $this->questionsRepository->findById($id);

    if (!$this->questionRow) {
      $this->error(self::ITEM_NOT_FOUND);
    } else {
      $this->testRow = $this->questionRow->ref('tests', 'test_id');
    }
  }

  /**
   * @param id test id
   */
  public function renderAll ($id) {
    $this->template->question = $this->questionRow;
    $this->template->answers = $this->questionRow->related('answers');
    $this->template->test = $this->testRow;
  }

  public function submittedAddForm ($form, $values) {
    $this->answersRepository->insert($values);
    $this->flashMessage(self::ITEM_ADD_SUCCESS);
    $this->redirect('all', $this->questionRow); 
  }

  protected function createComponentAddForm () {
    $form = new \Nette\Application\UI\Form;

    $form->addText('label', 'Odpoveď');
    $form->addHidden('question_id', $this->questionRow);
    $form->addCheckbox('correct', ' Správna odpoveď');
    $form->addSubmit('save', 'Uložiť');
    $form->onSuccess[] = [$this, 'submittedAddForm'];

    \App\FormHelper::setBootstrapRenderer($form);
    return $form;
  }
}
