<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\BreadcrumbControl;
use App\Forms\SearchFormFactory;
use App\Model\AlbumsRepository;
use App\Model\SectionsRepository;
use App\Model\SlidesRepository;

/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter
{
  /**
   * @var SlidesRepository
   */
  private $slidesRepository;

  /**
   * HomepagePresenter constructor.
   * @param AlbumsRepository $albumsRepository
   * @param SectionsRepository $sectionRepository
   * @param BreadcrumbControl $breadcrumbControl
   * @param SearchFormFactory $searchFormFactory
   * @param SlidesRepository $slidesRepository
   */
  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              BreadcrumbControl $breadcrumbControl,
                              SearchFormFactory $searchFormFactory,
                              SlidesRepository $slidesRepository)
  {
    parent::__construct($albumsRepository, $sectionRepository, $breadcrumbControl, $searchFormFactory);
    $this->slidesRepository = $slidesRepository;
  }

  /**
   * Passes data to default template
   */
  public function renderDefault() {
    $this->template->slides = $this->slidesRepository->findAll();
  }

}
