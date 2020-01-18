<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\BreadcrumbControl;
use App\Forms\QuestionFormFactory;
use App\Model\AlbumsRepository;
use App\Model\LevelsRepository;
use App\Model\QuestionsRepository;
use App\Model\SectionsRepository;
use App\Model\TestsRepository;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Application\UI\InvalidLinkException;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\ArrayHash;

/**
 * Class QuestionsPresenter
 * @package App\Presenters
 */
class QuestionsPresenter extends BasePresenter
{
  /** @var ActiveRow */
  private $testRow;

  /** @var ActiveRow **/ 
  private $questionRow;

  /**
   * @var TestsRepository
   */
  private $testsRepository;

  /**
   * @var QuestionsRepository
   */
  private $questionsRepository;
  /**
   * @var LevelsRepository
   */
  private $levelsRepository;

  /**
   * @var QuestionFormFactory
   */
  private $questionFormFactory;

  /**
   * QuestionsPresenter constructor.
   * @param AlbumsRepository $albumsRepository
   * @param SectionsRepository $sectionRepository
   * @param TestsRepository $testsRepository
   * @param QuestionsRepository $questionsRepository
   * @param LevelsRepository $levelsRepository
   * @param BreadcrumbControl $breadcrumbControl
   * @param QuestionFormFactory $questionFormFactory
   */
  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              TestsRepository $testsRepository,
                              QuestionsRepository $questionsRepository,
                              LevelsRepository $levelsRepository,
                              BreadcrumbControl $breadcrumbControl,
                              QuestionFormFactory $questionFormFactory)
  {
    parent::__construct($albumsRepository, $sectionRepository, $breadcrumbControl);
    $this->testsRepository = $testsRepository;
    $this->questionsRepository = $questionsRepository;
    $this->levelsRepository = $levelsRepository;
    $this->questionFormFactory = $questionFormFactory;
  }

  /**
   * @param int $id
   * @throws AbortException
   * @throws BadRequestException
   */
  public function actionAll (int $id): void
  {
    $this->guestRedirect();
    $this->testRow = $this->testsRepository->findById($id);

    if (!$this->testRow) {
      throw new BadRequestException(self::TEST_NOT_FOUND);
    }
  }

  /**
   * @param int $id
   * @throws InvalidLinkException
   */
  public function renderAll (int $id): void
  {
    $this->template->test = $this->testRow;
    $this->template->questions = $this->testRow->related('questions')->where('is_present', 1);
    $this['breadcrumb']->add('Testy', $this->link('Tests:all'));
    $this['breadcrumb']->add($this->testRow->label);
  }

  /**
   * @param ArrayHash $values
   * @throws AbortException
   */
  public function submittedAddForm (ArrayHash $values): void
  {
    $this->questionsRepository->insert($values);
    $this->flashMessage(self::ITEM_ADDED, self::INFO);
    $this->redirect('all', $this->testRow); 
  }

  /**
   * Creates add form control
   * @return Form
   */
  protected function createComponentAddForm (): Form
  {
    return $this->questionFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->guestRedirect();
      $this->submittedAddForm($values);
    });
  }
}
