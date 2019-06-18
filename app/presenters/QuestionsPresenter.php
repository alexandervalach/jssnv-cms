<?php

namespace App\Presenters;

use App\FormHelper;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;

namespace App\Presenters;

class QuestionsPresenter extends BasePresenter {

  /** @var ActiveRow */
  private $testRow;

  /** @var ActiveRow **/ 
  private $questionRow;

  /**
   * @param id test id 
   */
  public function actionAll ($id) {
    $this->userIsLogged();
    $this->testRow = $this->testsRepository->findById($id);

    if (!$this->testRow) {
      $this->error(self::TEST_NOT_FOUND);
    }
  }

  /**
   * @param id test id
   */
  public function renderAll ($id) {
    $this->template->test = $this->testRow;
    $this->template->questions = $this->testRow->related('questions');
  }

  public function submittedAddForm ($form, $values) {
    $this->questionsRepository->insert($values);
    $this->flashMessage(self::ITEM_ADD_SUCCESS);
    $this->redirect('all'); 
  }

  protected function createComponentAddForm () {
    $form = new \Nette\Application\UI\Form;
    $levels = $this->levelsRepository->getLevels();

    $form->addText('label', 'Znenie otázky');
    $form->addHidden('test_id', $this->testRow);
    $form->addSelect('level_id', 'Úroveň', $levels);
    $form->addSubmit('save', 'Uložiť');
    $form->onSuccess[] = [$this, 'submittedAddForm'];

    \App\FormHelper::setBootstrapRenderer($form);
    return $form;
  }
}
