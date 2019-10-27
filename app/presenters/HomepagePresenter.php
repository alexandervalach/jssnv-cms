<?php

namespace App\Presenters;

use App\Model\AlbumsRepository;
use App\Model\PostsRepository;
use App\Model\SectionsRepository;
use App\Model\SlidesRepository;

/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter {

  /**
   * @var SlidesRepository
   */
  private $slidesRepository;

  /**
   * @var PostsRepository
   */
  private $postsRepository;

  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              SlidesRepository $slidesRepository,
                              PostsRepository $postsRepository)
  {
    parent::__construct($albumsRepository, $sectionRepository);
    $this->slidesRepository = $slidesRepository;
    $this->postsRepository = $postsRepository;
  }

  /**
   *
   */
  public function renderDefault() {
    $this->template->slides = $this->slidesRepository->findAll();
    $this->template->posts = $this->postsRepository->findByValue('onHomepage', 1);
  }

}
