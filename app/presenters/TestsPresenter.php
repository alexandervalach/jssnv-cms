<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\BreadcrumbControl;
use App\Forms\FinishFormFactory;
use App\Forms\ModalRemoveFormFactory;
use App\Forms\QuestionFormFactory;
use App\Forms\SearchFormFactory;
use App\Forms\TestFormFactory;
use App\Helpers\TestHelper;
use App\Model\AlbumsRepository;
use App\Model\LevelsResultsRepository;
use App\Model\QuestionsRepository;
use App\Model\ResultsRepository;
use App\Model\SectionsRepository;
use App\Model\TestsRepository;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Application\UI\InvalidLinkException;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
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

  /** @var ArrayHash **/
  private $levelsQuestions;

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

  /**
   * @var ModalRemoveFormFactory
   */
  private $modalRemoveFormFactory;

  /**
   * @var QuestionFormFactory
   */
  private $questionFormFactory;

  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              BreadcrumbControl $breadcrumbControl,
                              SearchFormFactory $searchFormFactory,
                              TestsRepository $testsRepository,
                              ResultsRepository $resultsRepository,
                              QuestionsRepository $questionsRepository,
                              LevelsResultsRepository $levelsResultsRepository,
                              TestFormFactory $testFormFactory,
                              FinishFormFactory $finishFormFactory,
                              ModalRemoveFormFactory $modalRemoveFormFactory,
                              QuestionFormFactory $questionFormFactory)
  {
    parent::__construct($albumsRepository, $sectionRepository, $breadcrumbControl, $searchFormFactory);
    $this->testsRepository = $testsRepository;
    $this->resultsRepository = $resultsRepository;
    $this->levelsResultsRepository = $levelsResultsRepository;
    $this->questionsRepository = $questionsRepository;
    $this->testFormFactory = $testFormFactory;
    $this->finishFormFactory = $finishFormFactory;
    $this->modalRemoveFormFactory = $modalRemoveFormFactory;
    $this->questionFormFactory = $questionFormFactory;
  }

  /**
   * @throws AbortException
   */
  public function actionAll (): void
  {
  	// $this->guestRedirect();
    $this['breadcrumb']->add('Testy');
  }

  /**
   * Passes data to run template
   */
  public function renderAll (): void
  {
  	$this->template->tests = $this->testsRepository->findAll();
  }

  /**
   * @param int $id
   * @throws AbortException
   * @throws BadRequestException
   * @throws InvalidLinkException
   */
  public function actionView (int $id): void
  {
    $this->guestRedirect();
    $this->testRow = $this->testsRepository->findById($id);

    if (!$this->testRow) {
      throw new BadRequestException(self::TEST_NOT_FOUND);
    }

    $this->questions = $this->questionsRepository->findQuestions((int) $this->testRow->id);
    $this['testForm']->setDefaults($this->testRow);
    $this['breadcrumb']->add('Testy', $this->link('Tests:all'));
    $this['breadcrumb']->add($this->testRow->label);
  }

  /**
   * Passes data to view template
   * @param int $id
   */
  public function renderView (int $id): void
  {
    $this->template->test = $this->testRow;
    $this->template->questions = $this->questions;
  }

  /**
   * @param $id
   * @throws BadRequestException
   * @throws InvalidLinkException
   */
  public function actionRun (int $id): void
  {
    $this->testRow = $this->testsRepository->findById($id);

    if (!$this->testRow) {
      throw new BadRequestException(self::TEST_NOT_FOUND);
    }

    $this['testForm']->setDefaults($this->testRow);
    $this['breadcrumb']->add('Testy', $this->link('all'));
    $this['breadcrumb']->add($this->testRow->label);
    $this->questions = $this->questionsRepository->findQuestions((int)$this->testRow->id);
    $this->levelsQuestions = TestHelper::cookLevelsQuestions( $this->questions );
  }

  /**
   * Passes data to run template
   * @param $id
   */
  public function renderRun (int $id): void
  {
    $this->template->test = $this->testRow;
    $this->template->levelsQuestions = $this->levelsQuestions;
  }

  /**
   * Creates test control form
   * @return Form
   */
  protected function createComponentTestForm (): Form
  {
    return $this->testFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->guestRedirect();
      $this->getParameter('id') ? $this->submittedEditForm($values) : $this->submittedAddForm($values);
    });
  }

  protected function createComponentRemoveForm (): Form
  {
    return $this->modalRemoveFormFactory->create(function () {
      $this->guestRedirect();
      $this->submittedRemoveForm();
    });
  }

  /**
   * Creates add form control
   * @return Form
   */
  protected function createComponentAddQuestionForm (): Form
  {
    return $this->questionFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->guestRedirect();
      $this->submittedAddQuestionForm($values);
    });
  }

  /**
   * Creates finish form control
   * @return Form
   */
  protected function createComponentFinishForm (): Form
  {
    return $this->finishFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->submittedFinishForm($values);
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
   * @param ArrayHash $values
   * @throws AbortException
   */
  public function submittedAddQuestionForm (ArrayHash $values): void
  {
    $this->questionsRepository->insert($values);
    $this->flashMessage(self::ITEM_ADDED, self::INFO);
    $this->redirect('all', $this->testRow->id);
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
   * @throws AbortException
   */
  public function submittedRemoveForm (): void
  {
    $this->testsRepository->softDelete($this->testRow);
    $this->flashMessage(self::ITEM_REMOVED, self::INFO);
    $this->redirect('all');
  }

  public function submittedFinishForm (): void
  {
    $postData = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

    if (isset($postData['url']) && !empty($postData['url'])) {
      $this->redirect('Homepage:');
    }

    $result = TestHelper::evaluateTest($this->questions, $postData);

    if ($result->total_score == 0) {
      $this->flashMessage('Vyplňte správne aspoň jednu odpoveď, inak sa výsledok nezaznamená', self::INFO);
      $this->redirect('Tests:run', $this->testRow->id);
    }

    $resultRow = $this->resultsRepository->insert(
      [
        'test_id' => $this->testRow->id,
        'score' => $result->total_score,
        'email' => $result->email
      ]
    );

    $levelsResults = TestHelper::cookLevelsResults($result->levels, $resultRow->id);

    if (!empty($levelsResults)) {
      $this->levelsResultsRepository->insert($levelsResults);
    }

    $this->redirect('Results:view', $resultRow->id);
  }
}
