<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\BreadcrumbControl;
use App\Forms\SearchFormFactory;
use App\Model\AlbumsRepository;
use App\Model\ContentsRepository;
use App\Model\SectionsRepository;
use Nette\Database\Table\Selection;

/**
 * Class SearchPresenter
 * @package App\Presenters
 */
class SearchPresenter extends BasePresenter
{
  /**
   * @var ContentsRepository
   */
  private $contentsRepository;

  /**
   * @var Selection
   */
  private $results;

  /**
   * SearchPresenter constructor.
   * @param AlbumsRepository $albumsRepository
   * @param SectionsRepository $sectionRepository
   * @param BreadcrumbControl $breadcrumbControl
   * @param SearchFormFactory $searchForm
   * @param ContentsRepository $contentsRepository
   */
  public function __construct(AlbumsRepository $albumsRepository, SectionsRepository $sectionRepository, BreadcrumbControl $breadcrumbControl, SearchFormFactory $searchForm, ContentsRepository $contentsRepository)
  {
    parent::__construct($albumsRepository, $sectionRepository, $breadcrumbControl, $searchForm);
    $this->contentsRepository = $contentsRepository;
  }

  /**
   * Prepares data for default template
   * @param string|null $text
   */
  public function actionDefault (string $text = null): void
  {
    $this->results = $text ? $this->contentsRepository->findByText($text) : [];
    $this['breadcrumb']->add('Výsledky vyhľadávania');
  }

  /**
   * Passes data to default template
   * @param string|null $text
   */
  public function renderDefault (string $text = null): void
  {
    $this->template->results = $this->results;
    $this->template->needle = $text;
  }
}