<?php

namespace App\Presenters;

use App\FormHelper;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;

namespace App\Presenters;

use App\Helpers\FormHelper;
use App\Forms\TestFormFactory;
use App\Model\AlbumsRepository;
use App\Model\LevelsResultsRepository;
use App\Model\QuestionsRepository;
use App\Model\ResultsRepository;
use App\Model\SectionsRepository;
use App\Model\TestsRepository;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class TestsPresenter
 * @package App\Presenters
 */
class TestsPresenter extends BasePresenter {

  /** @var ActiveRow */
  private $testRow;

  /** @var Selection **/
  private $questions;

  /**
   * @var TestsRepository
   */
  private $testsRepository;

  /**
   * @var ResultsRepository
   */
  private $resultsRepository;

  /**
   * @var QuestionsRepository
   */
  private $questionsRepository;

  /**
   * @var LevelsResultsRepository
   */
  private $levelsResultsRepository;

  /**
   * @var TestFormFactory
   */
  private $testFormFactory;

  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              TestsRepository $testsRepository,
                              ResultsRepository $resultsRepository,
                              QuestionsRepository $questionsRepository,
                              LevelsResultsRepository $levelsResultsRepository,
                              TestFormFactory $testFormFactory)
  {
    parent::__construct($albumsRepository, $sectionRepository);
    $this->testsRepository = $testsRepository;
    $this->resultsRepository = $resultsRepository;
    $this->levelsResultsRepository = $levelsResultsRepository;
    $this->questionsRepository = $questionsRepository;
    $this->testFormFactory = $testFormFactory;
  }

  /**
   * @throws AbortException
   */
  public function actionAll () {
  	if (!$this->user->isLoggedIn()) {
  		$this->redirect('Homepage:');
  	}
  }

  /**
   *
   */
  public function renderAll () {
  	$this->template->tests = $this->testsRepository->findAll();
  }

  /**
   * @param $id
   * @throws BadRequestException
   */
  public function actionView ($id) {
    $this->testRow = $this->testsRepository->findById($id);

    if (!$this->testRow || !$this->testRow->is_present) {
      $this->error(self::TEST_NOT_FOUND);
    }

    $this['testForm']->setDefaults($this->testRow);
    $this->questions = $this->questionsRepository->findQuestions($this->testRow);
  }

  /**
   * @param $id
   */
  public function renderView ($id) {
    $this->template->test = $this->testRow;
    $this->template->questions = $this->questions;
  }

  /**
   * @param $form
   * @param $values
   * @throws AbortException
   */
  private function submittedAddForm ($values) {
    $this->testsRepository->insert($values);
    $this->flashMessage(self::ITEM_ADDED);
    $this->redirect('all');
  }

  /**
   * @param $form
   * @param $values
   * @throws AbortException
   */
  private function submittedEditForm ($values) {
    $this->testRow->update($values);
    $this->flashMessage(self::ITEM_UPDATED);
    $this->redirect('all');
  }

  /**
   * @param $form
   * @param $values
   * @throws AbortException
   */
  public function submittedFinishForm (Form $form, ArrayHash $values) {
  	$postData = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

    if (isset($postData['url']) && !empty($postData['url'])) {
      $this->redirect('Homepage:');
    }

  	$result = $this->evaluateTest($postData);
    $this->redirect('Results:view', $result->id);
  }

  /**
   * @return Form
   */
  protected function createComponentTestForm () {
    return $this->testFormFactory->create(function (Form $form, ArrayHash $values) {
      $id = $this->getParameter('id');

      if ($id) {
        $this->submittedEditForm($values);
      } else {
        $this->submittedAddForm($values);
      }
    });
  }

  /**
   * @return Form
   */
  protected function createComponentFinishForm () {
    $form = new Form();
    $form->addText('email', 'E-mail')
      ->addCondition(Form::EMAIL, true);
    $form->addText('url', 'Mňam, mňa, toto vyplní len robot')
      ->setHtmlAttribute('style', 'opacity: 0; display: inline')
      ->setDefaultValue('');
    $form->addSubmit('finish', 'Ukončiť test');
    $form->onSuccess[] = [$this, 'submittedFinishForm'];

    FormHelper::setBootstrapFormRenderer($form);
    return $form;
  }

  /**
   * @param $postData
   * @return bool|int|ActiveRow
   */
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
