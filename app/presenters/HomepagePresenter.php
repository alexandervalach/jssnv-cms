<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\BreadcrumbControl;
use App\Model\AlbumsRepository;
use App\Model\PostsRepository;
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
   * @param SlidesRepository $slidesRepository
   * @param BreadcrumbControl $breadcrumbControl
   */
  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              SlidesRepository $slidesRepository,
                              BreadcrumbControl $breadcrumbControl)
  {
    parent::__construct($albumsRepository, $sectionRepository, $breadcrumbControl);
    $this->slidesRepository = $slidesRepository;
  }

  /**
   * Passes data to default template
   */
  public function renderDefault() {
    $this->template->slides = $this->slidesRepository->findAll();
  }

}
