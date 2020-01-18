<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\BreadcrumbControl;
use App\Model\AlbumsRepository;
use App\Model\AnswersRepository;
use App\Model\QuestionsRepository;
use App\Model\SectionsRepository;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;

/**
 * Class AnswersPresenter
 * @package App\Presenters
 */
class AnswersPresenter extends BasePresenter
{
  /** @var ActiveRow **/ 
  private $questionRow;
  
  /** @var ActiveRow */
  private $testRow;
  /**
   * @var QuestionsRepository
   */
  private $questionsRepository;
  /**
   * @var AnswersRepository
   */
  private $answersRepository;

  /**
   * AnswersPresenter constructor.
   * @param AlbumsRepository $albumsRepository
   * @param SectionsRepository $sectionRepository
   * @param QuestionsRepository $questionsRepository
   * @param AnswersRepository $answersRepository
   * @param BreadcrumbControl $breadcrumbControl
   */
  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              QuestionsRepository $questionsRepository,
                              AnswersRepository $answersRepository,
                              BreadcrumbControl $breadcrumbControl)
  {
    parent::__construct($albumsRepository, $sectionRepository, $breadcrumbControl);
    $this->questionsRepository = $questionsRepository;
    $this->answersRepository  = $answersRepository;
  }

  /**
   * @param int $id
   * @throws AbortException
   * @throws BadRequestException
   */
  public function actionAll (int $id): void
  {
    $this->userIsLogged();
    $this->questionRow = $this->questionsRepository->findById($id);

    if (!$this->questionRow) {
      $this->error(self::ITEM_NOT_FOUND);
    } else {
      $this->testRow = $this->questionRow->ref('tests', 'test_id');
    }
  }

  /**
   * @param int $id
   */
  public function renderAll (int $id): void
  {
    $this->template->question = $this->questionRow;
    $this->template->answers = $this->questionRow->related('answers');
    $this->template->test = $this->testRow;
  }

  /**
   * @param $form
   * @param $values
   * @throws AbortException
   */
  public function submittedAddForm ($form, $values) {
    $this->answersRepository->insert($values);
    $this->flashMessage(self::ITEM_ADD_SUCCESS);
    $this->redirect('all', $this->questionRow); 
  }

  /**
   * @return Form
   */
  protected function createComponentAddForm () {
    $form = new Form();

    $form->addText('label', 'Odpoveď');
    $form->addHidden('question_id', $this->questionRow);
    $form->addCheckbox('correct', ' Správna odpoveď');
    $form->addSubmit('save', 'Uložiť');
    $form->onSuccess[] = [$this, 'submittedAddForm'];

    FormHelper::setBootstrapRenderer($form);
    return $form;
  }
}
