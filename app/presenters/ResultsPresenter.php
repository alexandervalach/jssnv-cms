<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\BreadcrumbControl;
use App\Forms\SearchFormFactory;
use App\Model\AlbumsRepository;
use App\Model\LevelsResultsRepository;
use App\Model\ResultsRepository;
use App\Model\SectionsRepository;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\InvalidLinkException;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\ArrayHash;

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
   * @var ArrayHash
   */
  private $results;

  /**
   * ResultsPresenter constructor.
   * @param AlbumsRepository $albumsRepository
   * @param SectionsRepository $sectionRepository
   * @param SearchFormFactory $searchFormFactory
   * @param BreadcrumbControl $breadcrumbControl
   * @param ResultsRepository $resultsRepository
   * @param LevelsResultsRepository $levelsResultsRepository
   */
  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              SearchFormFactory $searchFormFactory,
                              BreadcrumbControl $breadcrumbControl,
                              ResultsRepository $resultsRepository,
                              LevelsResultsRepository $levelsResultsRepository)
  {
    parent::__construct($albumsRepository, $sectionRepository, $breadcrumbControl, $searchFormFactory);
    $this->resultsRepository = $resultsRepository;
    $this->levelsResultsRepository = $levelsResultsRepository;
  }

  /**
   * @throws AbortException
   * @throws InvalidLinkException
   */
  public function actionAll (): void
  {
    $this->guestRedirect();
    $results = $this->resultsRepository->findAllAndOrder();
    $data = [];

    foreach ($results as $result) {
      $data[] = [
        'data' => $result,
        'levels' => $result->related('levels_results'),
        'test' => $result->ref('tests', 'test_id')
      ];
    }

    $this->results = ArrayHash::from($data);
    $this['breadcrumb']->add('Testy', $this->link('Tests:all'));
    $this['breadcrumb']->add('Výsledky testov');
  }

  /**
   *
   */
  public function renderAll (): void
  {
    $this->template->results = $this->results;
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
