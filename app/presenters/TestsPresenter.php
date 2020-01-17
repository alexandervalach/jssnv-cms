<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Forms\FinishFormFactory;
use App\Forms\TestFormFactory;
use App\Model\AlbumsRepository;
use App\Model\LevelsResultsRepository;
use App\Model\QuestionsRepository;
use App\Model\ResultsRepository;
use App\Model\SectionsRepository;
use App\Model\TestsRepository;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class TestsPresenter
 * @package App\Presenters
 */
class TestsPresenter extends BasePresenter
{
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

  /**
   * @var FinishFormFactory
   */
  private $finishFormFactory;

  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              TestsRepository $testsRepository,
                              ResultsRepository $resultsRepository,
                              QuestionsRepository $questionsRepository,
                              LevelsResultsRepository $levelsResultsRepository,
                              TestFormFactory $testFormFactory,
                              FinishFormFactory $finishFormFactory)
  {
    parent::__construct($albumsRepository, $sectionRepository);
    $this->testsRepository = $testsRepository;
    $this->resultsRepository = $resultsRepository;
    $this->levelsResultsRepository = $levelsResultsRepository;
    $this->questionsRepository = $questionsRepository;
    $this->testFormFactory = $testFormFactory;
    $this->finishFormFactory = $finishFormFactory;
  }

  /**
   * @throws AbortException
   */
  public function actionAll (): void
  {
  	$this->userIsLogged();
  }

  /**
   *
   */
  public function renderAll (): void
  {
  	$this->template->tests = $this->testsRepository->findAll();
  }

  /**
   * @param $id
   * @throws BadRequestException
   */
  public function actionView (int $id): void
  {
    $this->testRow = $this->testsRepository->findById($id);

    if (!$this->testRow) {
      throw new BadRequestException(self::TEST_NOT_FOUND);
    }

    $this['testForm']->setDefaults($this->testRow);
    $this->questions = $this->questionsRepository->findQuestions($this->testRow->id);
  }

  /**
   * @param $id
   */
  public function renderView (int $id): void
  {
    $this->template->test = $this->testRow;
    $this->template->questions = $this->questions;
  }

  /**
   * @return Form
   */
  protected function createComponentTestForm (): Form
  {
    return $this->testFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->getParameter('id') ? $this->submittedEditForm($values) : $this->submittedAddForm($values);
    });
  }

  /**
   * @return Form
   */
  protected function createComponentFinishForm (): Form
  {
    return $this->finishFormFactory->create(function (Form $form, ArrayHash $values) {
      $postData = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      if (isset($postData['url']) && !empty($postData['url'])) {
        $this->redirect('Homepage:');
      }

      $result = $this->evaluateTest($postData);
      $this->redirect('Results:view', $result->id);
    });
  }

  /**
   * @param ArrayHash $values
   * @throws AbortException
   */
  private function submittedAddForm (ArrayHash $values): void
  {
    $this->testsRepository->insert($values);
    $this->flashMessage(self::ITEM_ADDED, self::INFO);
    $this->redirect('all');
  }

  /**
   * @param $values
   * @throws AbortException
   */
  private function submittedEditForm (ArrayHash $values): void
  {
    $this->testRow->update($values);
    $this->flashMessage(self::ITEM_UPDATED, self::INFO);
    $this->redirect('all');
  }


  /**
   * @param $postData
   * @return bool|int|ActiveRow
   */
  protected function evaluateTest ($postData)
  {
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
