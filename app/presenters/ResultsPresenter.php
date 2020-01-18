<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\BreadcrumbControl;
use App\Model\AlbumsRepository;
use App\Model\LevelsResultsRepository;
use App\Model\ResultsRepository;
use App\Model\SectionsRepository;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Database\Table\ActiveRow;

/**
 * Class ResultsPresenter
 * @package App\Presenters
 */
class ResultsPresenter extends BasePresenter
{
  /** @var array */
  private $levelsResults;

  /** @var ActiveRow */
  private $resultRow;

  /** @var ResultsRepository */
  private $resultsRepository;

  /**
   * @var LevelsResultsRepository
   */
  private $levelsResultsRepository;

  /**
   * ResultsPresenter constructor.
   * @param AlbumsRepository $albumsRepository
   * @param SectionsRepository $sectionRepository
   * @param ResultsRepository $resultsRepository
   * @param LevelsResultsRepository $levelsResultsRepository
   * @param BreadcrumbControl $breadcrumbControl
   */
  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              ResultsRepository $resultsRepository,
                              LevelsResultsRepository $levelsResultsRepository,
                              BreadcrumbControl $breadcrumbControl)
  {
    parent::__construct($albumsRepository, $sectionRepository, $breadcrumbControl);
    $this->resultsRepository = $resultsRepository;
    $this->levelsResultsRepository = $levelsResultsRepository;
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
    $results = $this->resultsRepository->findAll();
    $data = [];

    foreach ($results as $result) {
      $data[] = array(
        'data' => $result,
        'levels' => $result->related('levels_results'),
        'test' => $result->ref('tests', 'test_id')
      );
    }

    $this['breadcrumb']->add('Testy', $this->link('Tests:all'));
    $this['breadcrumb']->add('Výsledky testov');
    $this->template->results = $data;
  }

  /**
   * @param $id
   * @throws BadRequestException
   */
  public function actionView (int $id): void
  {
      $this->resultRow = $this->resultsRepository->findById($id);
      $this->levelsResults = array();

      if (!$this->resultRow) {
        throw new BadRequestException(self::ITEM_NOT_FOUND);
      }

      $levelsResults = $this->levelsResultsRepository->findAll()->where('result_id', $this->resultRow->id);

      foreach ($levelsResults as $result) {
        $this->levelsResults[$result->level_id]['score'] = $result->score;
        $this->levelsResults[$result->level_id]['label'] = $result->ref('levels', 'level_id')->label;
      }
    }

  /**
   * @param $id
   */
  public function renderView (int $id): void
  {
    $this->template->result = $this->resultRow;
    $this->template->levels = $this->levelsResults;
    $this->template->test = $this->resultRow->ref('tests', 'test_id');
  }
}
