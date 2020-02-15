<?php


namespace App\Presenters;

use App\Components\BreadcrumbControl;
use App\Forms\EditNoticeFormFactory;
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

  /**
   * @var EditNoticeFormFactory
   */
  private $editNoticeFormFactory;

  public function __construct(AlbumsRepository $albumsRepository, SectionsRepository $sectionRepository, BreadcrumbControl $breadcrumbControl, SearchFormFactory $searchForm, NoticesRepository $noticesRepository, NoticeFormFactory $noticeFormFactory, EditNoticeFormFactory $editNoticeFormFactory)
  {
    parent::__construct($albumsRepository, $sectionRepository, $breadcrumbControl, $searchForm);
    $this->noticesRepository = $noticesRepository;
    $this->noticeFormFactory = $noticeFormFactory;
    $this->editNoticeFormFactory = $editNoticeFormFactory;
  }

  public function renderAll (): void
  {
    $this->template->notices = $this->noticesRepository->findAllAndOrder();
  }

  public function actionEdit (int $id): void
  {
    $this->guestRedirect();
    $this->noticeRow = $this->noticesRepository->findById($id);

    if (!$this->noticeRow) {
      $this->error(self::ITEM_NOT_FOUND);
    }

    $this['editForm']->setDefaults($this->noticeRow);
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

  protected function createComponentEditForm (): Form
  {
    return $this->editNoticeFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->guestRedirect();
      $this->submittedEditForm($values);
    });
  }

  private function submittedAddForm (ArrayHash $values): void
  {
    $this->noticesRepository->insert($values);
    $this->flashMessage(self::ITEM_ADDED, self::INFO);
    $this->redirect('all');
  }

  private function submittedEditForm (ArrayHash $values): void
  {
    $this->noticeRow->update($values);
    $this->flashMessage(self::ITEM_UPDATED, self::INFO);
    $this->redirect('all');
  }

  private function submittedRemoveForm (): void
  {
    $this->noticesRepository->softDelete($this->noticeRow);
    $this->flashMessage(self::ITEM_REMOVED, self::INFO);
    $this->redirect('all');
  }

}