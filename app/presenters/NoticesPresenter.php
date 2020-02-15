<?php


namespace App\Presenters;


use App\Components\BreadcrumbControl;
use App\Forms\NoticeFormFactory;
use App\Forms\SearchFormFactory;
use App\Model\AlbumsRepository;
use App\Model\NoticesRepository;
use App\Model\SectionsRepository;
use Nette\Database\Table\ActiveRow;
use Nette\Forms\Form;
use Nette\Utils\ArrayHash;

class NoticesPresenter extends BasePresenter
{
  /**
   * @var NoticesRepository
   */
  private $noticesRepository;

  /**
   * @var ActiveRow|null
   */
  private $noticeRow;

  /**
   * @var NoticeFormFactory
   */
  private $noticeFormFactory;

  public function __construct(AlbumsRepository $albumsRepository, SectionsRepository $sectionRepository, BreadcrumbControl $breadcrumbControl, SearchFormFactory $searchForm, NoticesRepository $noticesRepository, NoticeFormFactory $noticeFormFactory)
  {
    parent::__construct($albumsRepository, $sectionRepository, $breadcrumbControl, $searchForm);
    $this->noticesRepository = $noticesRepository;
    $this->noticeFormFactory = $noticeFormFactory;
  }

  public function renderAll (): void
  {
    $this->template->notices = $this->noticesRepository->findAll();
  }

  public function actionEdit (int $id): void
  {
    $this->guestRedirect();
    $this->noticeRow = $this->noticesRepository->findById($id);

    if (!$this->noticeRow) {
      $this->error(self::ITEM_NOT_FOUND);
    }
  }

  public function renderEdit (): void
  {
    $this->template->notice = $this->noticeRow;
  }

  public function actionRemove (int $id): void
  {
    $this->guestRedirect();
    $this->noticeRow = $this->noticesRepository->findById($id);

    if (!$this->noticeRow) {
      $this->error(self::ITEM_NOT_FOUND);
    }

    $this->submittedRemoveForm();
  }

  protected function createComponentAddForm (): Form
  {
    return $this->noticeFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->guestRedirect();
      $this->submittedAddForm($values);
    });
  }

  private function submittedAddForm (ArrayHash $values): void
  {
    $this->noticesRepository->insert($values);
    $this->flashMessage(self::ITEM_ADDED, self::INFO);
    $this->redirect('all');
  }

  private function submittedRemoveForm (): void
  {
    $this->noticesRepository->softDelete($this->noticeRow);
    $this->flashMessage(self::ITEM_REMOVED, self::INFO);
    $this->redirect('all');
  }

}