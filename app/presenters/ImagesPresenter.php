<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\BreadcrumbControl;
use App\Forms\SearchFormFactory;
use App\Model\AlbumsRepository;
use App\Model\ImagesRepository;
use App\Model\SectionsRepository;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Database\Table\ActiveRow;

/**
 * Class ImagesPresenter
 * @package App\Presenters
 */
class ImagesPresenter extends BasePresenter
{
  /** @var ActiveRow */
  private $albumRow;

  /** @var ActiveRow */
  private $imageRow;

  /**
   * @var ImagesRepository
   */
  private $imagesRepository;

  /**
   * ImagesPresenter constructor.
   * @param AlbumsRepository $albumsRepository
   * @param SectionsRepository $sectionRepository
   * @param BreadcrumbControl $breadcrumbControl
   * @param SearchFormFactory $searchFormFactory
   * @param ImagesRepository $imagesRepository
   */
  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              BreadcrumbControl $breadcrumbControl,
                              SearchFormFactory $searchFormFactory,
                              ImagesRepository $imagesRepository)
  {
    parent::__construct($albumsRepository, $sectionRepository, $breadcrumbControl, $searchFormFactory);
    $this->imagesRepository = $imagesRepository;
  }

  /**
   * @param $id
   * @throws BadRequestException
   * @throws AbortException
   */
  public function actionRemove(int $id): void
  {
    $this->guestRedirect();
    $this->imageRow = $this->imagesRepository->findById($id);

    if (!$this->imageRow) {
      throw new BadRequestException(self::ITEM_NOT_FOUND);
    }

    $this->albumRow = $this->imageRow->ref('albums', 'album_id');

    if (!$this->albumRow) {
      throw new BadRequestException(self::ITEM_NOT_FOUND);
    }

    $this->submittedRemoveForm();
  }

  /**
   * @param $id
   * @throws BadRequestException
   * @throws AbortException
   */
  public function actionThumbnail(int $id): void
  {
    $this->guestRedirect();
    $this->imageRow = $this->imagesRepository->findById($id);

    if (!$this->imageRow) {
      $this->error(self::ITEM_NOT_FOUND);
    }

    $this->albumRow = $this->imageRow->ref('albums', 'album_id');

    if (!$this->albumRow) {
      $this->error(self::ITEM_NOT_FOUND);
    }

    $this->submittedThumbnailForm();
  }

  /**
   * @throws AbortException
   */
  private function submittedRemoveForm(): void
  {
    $this->imagesRepository->softDelete($this->imageRow);
    $this->flashMessage(self::ITEM_REMOVED, self::INFO);
    $this->redirect('Albums:view', $this->albumRow->id);
  }

  /**
   * @throws AbortException
   */
  private function submittedThumbnailForm(): void
  {
    $this->albumRow->update(array('thumbnail' => $this->imageRow->name));
    $this->flashMessage(self::ITEM_UPDATED, self::INFO);
    $this->redirect('Albums:all');
  }

}
