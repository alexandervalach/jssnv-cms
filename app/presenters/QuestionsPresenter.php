<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\AlbumsRepository;
use App\Model\LevelsRepository;
use App\Model\QuestionsRepository;
use App\Model\SectionsRepository;
use App\Model\TestsRepository;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Database\Table\ActiveRow;

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
   * QuestionsPresenter constructor.
   * @param AlbumsRepository $albumsRepository
   * @param SectionsRepository $sectionRepository
   * @param TestsRepository $testsRepository
   * @param QuestionsRepository $questionsRepository
   * @param LevelsRepository $levelsRepository
   */
  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              TestsRepository $testsRepository,
                              QuestionsRepository $questionsRepository,
                              LevelsRepository $levelsRepository)
  {
    parent::__construct($albumsRepository, $sectionRepository);
    $this->testsRepository = $testsRepository;
    $this->questionsRepository = $questionsRepository;
    $this->levelsRepository = $levelsRepository;
  }

  /**
   * @param id test id
   * @throws AbortException
   * @throws BadRequestException
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

  /**
   * @param $form
   * @param $values
   * @throws AbortException
   */
  public function submittedAddForm ($form, $values) {
    $this->questionsRepository->insert($values);
    $this->flashMessage(self::ITEM_ADD_SUCCESS);
    $this->redirect('all', $this->testRow); 
  }

  /**
   * @return Form
   */
  protected function createComponentAddForm () {
    $form = new Form;
    $levels = $this->levelsRepository->getLevels();

    $form->addText('label', 'Znenie otázky');
    $form->addHidden('test_id', $this->testRow);
    $form->addSelect('level_id', 'Úroveň', $levels);
    $form->addSubmit('save', 'Uložiť');
    $form->onSuccess[] = [$this, 'submittedAddForm'];

    FormHelper::setBootstrapFormRenderer($form);
    return $form;
  }
}
