<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\BreadcrumbControl;
use App\Forms\ModalRemoveFormFactory;
use App\Forms\QuestionFormFactory;
use App\Forms\TestFormFactory;
use App\Model\AlbumsRepository;
use App\Model\LevelsRepository;
use App\Model\QuestionsRepository;
use App\Model\SectionsRepository;
use App\Model\TestsRepository;
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
   * @var QuestionFormFactory
   */
  private $questionFormFactory;

  /**
   * @var TestFormFactory
   */
  private $testFormFactory;

  /**
   * @var ModalRemoveFormFactory
   */
  private $modalRemoveFormFactory;

  /**
   * QuestionsPresenter constructor.
   * @param AlbumsRepository $albumsRepository
   * @param SectionsRepository $sectionRepository
   * @param TestsRepository $testsRepository
   * @param QuestionsRepository $questionsRepository
   * @param LevelsRepository $levelsRepository
   * @param BreadcrumbControl $breadcrumbControl
   * @param QuestionFormFactory $questionFormFactory
   * @param TestFormFactory $testFormFactory
   * @param ModalRemoveFormFactory $modalRemoveFormFactory
   */
  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              TestsRepository $testsRepository,
                              QuestionsRepository $questionsRepository,
                              LevelsRepository $levelsRepository,
                              BreadcrumbControl $breadcrumbControl,
                              QuestionFormFactory $questionFormFactory,
                              TestFormFactory $testFormFactory,
                              ModalRemoveFormFactory $modalRemoveFormFactory)
  {
    parent::__construct($albumsRepository, $sectionRepository, $breadcrumbControl);
    $this->testsRepository = $testsRepository;
    $this->questionsRepository = $questionsRepository;
    $this->levelsRepository = $levelsRepository;
    $this->questionFormFactory = $questionFormFactory;
    $this->testFormFactory = $testFormFactory;
    $this->modalRemoveFormFactory = $modalRemoveFormFactory;
  }
}
