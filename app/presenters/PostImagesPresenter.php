<?php

namespace App\Presenters;

use App\Model\AlbumsRepository;
use App\Model\ImagesRepository;
use App\Model\PostsRepository;
use App\Model\SectionsRepository;

/**
 * Class PostImagesPresenter
 * @package App\Presenters
 */
class PostImagesPresenter extends BasePresenter {

  /**
   * @var PostsRepository
   */
  private $postsRepository;

  /**
   * @var ImagesRepository
   */
  private $postImagesRepository;

  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              ImagesRepository $postImagesRepository)
    {
      parent::__construct($albumsRepository, $sectionRepository);
      $this->postImagesRepository = $postImagesRepository;
    }

}
