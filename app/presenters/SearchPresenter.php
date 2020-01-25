<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\BreadcrumbControl;
use App\Forms\SearchFormFactory;
use App\Model\AlbumsRepository;
use App\Model\SectionsRepository;

class SearchPresenter extends BasePresenter
{
  public function __construct(AlbumsRepository $albumsRepository, SectionsRepository $sectionRepository, BreadcrumbControl $breadcrumbControl, SearchFormFactory $searchForm)
  {
    parent::__construct($albumsRepository, $sectionRepository, $breadcrumbControl, $searchForm);
  }

  public function actionAll (string $expression): void
  {

  }

  public function renderAll (string $expression): void
  {

  }
}