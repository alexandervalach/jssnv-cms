<?php

declare(strict_types=1);

namespace App\Presenters;

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
   */
  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              SlidesRepository $slidesRepository)
  {
    parent::__construct($albumsRepository, $sectionRepository);
    $this->slidesRepository = $slidesRepository;
  }

  /**
   *
   */
  public function renderDefault() {
    $this->template->slides = $this->slidesRepository->findAll();
  }

}
