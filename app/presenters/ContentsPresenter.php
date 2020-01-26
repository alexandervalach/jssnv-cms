<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\BreadcrumbControl;
use App\Forms\SearchFormFactory;
use App\Model\AlbumsRepository;
use App\Model\ContentsRepository;
use App\Model\SectionsRepository;
use Nette\Database\Table\ActiveRow;

class ContentsPresenter extends BasePresenter
{
  /**
   * @var ContentsRepository
   */
  private $contentsRepository;

  /**
   * @var ActiveRow|null
   */
  private $contentRow;

  public function __construct(AlbumsRepository $albumsRepository, SectionsRepository $sectionRepository, BreadcrumbControl $breadcrumbControl, SearchFormFactory $searchForm, ContentsRepository $contentsRepository)
  {
    parent::__construct($albumsRepository, $sectionRepository, $breadcrumbControl, $searchForm);
    $this->contentsRepository = $contentsRepository;
  }

  public function actionRemove (int $id): void
  {
    $this->guestRedirect();
    $this->contentRow = $this->contentsRepository->findById($id);

    if (!$this->contentRow) {
      $this->error(self::ITEM_NOT_FOUND);
    }

    $this->submittedRemoveForm($id);
  }

  private function submittedRemoveForm (int $id): void
  {
    $this->contentsRepository->softDelete($id);
    $this->flashMessage(self::ITEM_REMOVED, self::INFO);
    $this->redirect('Sections:view', $this->contentRow->section_id);
  }
}