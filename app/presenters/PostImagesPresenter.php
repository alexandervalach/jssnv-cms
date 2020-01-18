<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\BreadcrumbControl;
use App\Model\AlbumsRepository;
use App\Model\ImagesRepository;
use App\Model\PostsRepository;
use App\Model\SectionsRepository;

/**
 * Class PostImagesPresenter
 * @package App\Presenters
 */
class PostImagesPresenter extends BasePresenter
{
  /**
   * @var ImagesRepository
   */
  private $postImagesRepository;

  /**
   * PostImagesPresenter constructor.
   * @param AlbumsRepository $albumsRepository
   * @param SectionsRepository $sectionRepository
   * @param ImagesRepository $postImagesRepository
   * @param BreadcrumbControl $breadcrumbControl
   */
  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              ImagesRepository $postImagesRepository,
                              BreadcrumbControl $breadcrumbControl)
    {
      parent::__construct($albumsRepository, $sectionRepository, $breadcrumbControl);
      $this->postImagesRepository = $postImagesRepository;
    }

}
