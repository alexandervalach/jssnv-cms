<?php


namespace App\Presenters;

use App\Components\BreadcrumbControl;
use App\Forms\EditNoticeFormFactory;
use App\Forms\NoticeFormFactory;
use App\Forms\SearchFormFactory;
use App\Model\AlbumsRepository;
use App\Model\NoticesRepository;
use App\Model\SectionsRepository;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Database\Table\ActiveRow;
use Nette\Forms\Form;
use Nette\Utils\ArrayHash;

/**
 * Class NoticesPresenter
 * @package App\Presenters
 */
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

  /**
   * NoticesPresenter constructor.
   * @param AlbumsRepository $albumsRepository
   * @param SectionsRepository $sectionRepository
   * @param BreadcrumbControl $breadcrumbControl
   * @param SearchFormFactory $searchForm
   * @param NoticesRepository $noticesRepository
   * @param NoticeFormFactory $noticeFormFactory
   * @param EditNoticeFormFactory $editNoticeFormFactory
   */
  public function __construct(AlbumsRepository $albumsRepository, SectionsRepository $sectionRepository, BreadcrumbControl $breadcrumbControl, SearchFormFactory $searchForm, NoticesRepository $noticesRepository, NoticeFormFactory $noticeFormFactory, EditNoticeFormFactory $editNoticeFormFactory)
  {
    parent::__construct($albumsRepository, $sectionRepository, $breadcrumbControl, $searchForm);
    $this->noticesRepository = $noticesRepository;
    $this->noticeFormFactory = $noticeFormFactory;
    $this->editNoticeFormFactory = $editNoticeFormFactory;
  }

  /**
   *
   */
  public function actionAll (): void
  {
    $this['breadcrumb']->add('Oznamy');
  }

  /**
   *
   */
  public function renderAll (): void
  {
    $this->template->notices = $this->noticesRepository->findAllAndOrder();
  }

  /**
   * @param int $id
   * @throws AbortException
   * @throws BadRequestException
   */
  public function actionEdit (int $id): void
  {
    $this->guestRedirect();
    $this->noticeRow = $this->noticesRepository->findById($id);

    if (!$this->noticeRow) {
      $this->error(self::ITEM_NOT_FOUND);
    }

    $this['breadcrumb']->add('Oznamy', $this->link('Notices:all'));
    $this['breadcrumb']->add($this->noticeRow->title);

    $this['editForm']->setDefaults($this->noticeRow);
  }

  /**
   *
   */
  public function renderEdit (): void
  {
    $this->template->notice = $this->noticeRow;
  }

  /**
   * @param int $id
   * @throws AbortException
   * @throws BadRequestException
   */
  public function actionRemove (int $id): void
  {
    $this->guestRedirect();
    $this->noticeRow = $this->noticesRepository->findById($id);

    if (!$this->noticeRow) {
      $this->error(self::ITEM_NOT_FOUND);
    }

    $this->submittedRemoveForm();
  }

  /**
   * @return Form
   */
  protected function createComponentAddForm (): Form
  {
    return $this->noticeFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->guestRedirect();
      $this->submittedAddForm($values);
    });
  }

  /**
   * @return Form
   */
  protected function createComponentEditForm (): Form
  {
    return $this->editNoticeFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->guestRedirect();
      $this->submittedEditForm($values);
    });
  }

  /**
   * @param ArrayHash $values
   * @throws AbortException
   */
  private function submittedAddForm (ArrayHash $values): void
  {
    $this->noticesRepository->insert($values);
    $this->flashMessage(self::ITEM_ADDED, self::INFO);
    $this->redirect('all');
  }

  /**
   * @param ArrayHash $values
   * @throws AbortException
   */
  private function submittedEditForm (ArrayHash $values): void
  {
    $this->noticeRow->update($values);
    $this->flashMessage(self::ITEM_UPDATED, self::INFO);
    $this->redirect('all');
  }

  /**
   * @throws AbortException
   */
  private function submittedRemoveForm (): void
  {
    $this->noticesRepository->softDelete($this->noticeRow);
    $this->flashMessage(self::ITEM_REMOVED, self::INFO);
    $this->redirect('all');
  }

}