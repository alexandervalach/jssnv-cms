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
    $this->guestRedirect();
    $this->questionRow = $this->questionsRepository->findById($id);

    if (!$this->questionRow) {
      $this->error(self::ITEM_NOT_FOUND);
    }
    $this->testRow = $this->questionRow->ref('tests', 'test_id');
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
  public function submittedAddForm ($form, $values): void
  {
    $this->guestRedirect();
    $this->answersRepository->insert($values);
    $this->flashMessage(self::ITEM_ADDED, self::SUCCESS);
    $this->redirect('all', $this->questionRow->id);
  }

  /**
   * Creates add form component
   * @return Form
   */
  protected function createComponentAddForm (): Form
  {
    $form = new Form();
    $form->addText('label', 'Odpoveď*')
        ->setRequired()
        ->addRule(FORM::MAX_LENGTH, 'Dĺžka odpovede môže byť max 255 znakov', 255);
    $form->addHidden('question_id', (string) $this->questionRow->id);
    $form->addCheckbox('correct', ' Správna odpoveď');
    $form->addSubmit('save', 'Uložiť');
    $form->addSubmit('cancel', 'Zrušiť')
        ->setHtmlAttribute('class', 'btn btn-warning')
        ->setHtmlAttribute('data-dismiss', 'modal');
    $form->onSuccess[] = [$this, 'submittedAddForm'];
    FormHelper::setBootstrapRenderer($form);
    return $form;
  }
}
