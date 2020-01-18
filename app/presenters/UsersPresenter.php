<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\BreadcrumbControl;
use App\Forms\EditUserFormFactory;
use App\Forms\ModalRemoveFormFactory;
use App\Forms\PasswordFormFactory;
use App\Forms\UserFormFactory;
use App\Model\AlbumsRepository;
use App\Model\SectionsRepository;
use App\Model\UsersRepository;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\Form;
use Nette\Application\UI\InvalidLinkException;
use Nette\Database\Table\ActiveRow;
use Nette\Security\Passwords;
use Nette\Utils\ArrayHash;

/**
 * Class UserPresenter
 * @package App\Presenters
 */
class UsersPresenter extends BasePresenter
{
  const ROOT = 'admin';

  /** @var ActiveRow */
  private $userRow;

  /**
   * @var UsersRepository
   */
  private $usersRepository;

  /**
   * @var Passwords
   */
  private $passwords;

  /**
   * @var UserFormFactory
   */
  private $userFormFactory;

  /**
   * @var ModalRemoveFormFactory
   */
  private $modalRemoveFormFactory;

  /**
   * @var PasswordFormFactory
   */
  private $passwordFormFactory;

  /**
   * @var EditUserFormFactory
   */
  private $editUserFormFactory;

  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              UsersRepository $usersRepository,
                              Passwords $passwords,
                              UserFormFactory $userFormFactory,
                              ModalRemoveFormFactory $modalRemoveFormFactory,
                              PasswordFormFactory $passwordFormFactory,
                              EditUserFormFactory $editUserFormFactory,
                              BreadcrumbControl $breadcrumbControl)
  {
    parent::__construct($albumsRepository, $sectionRepository, $breadcrumbControl);
    $this->usersRepository = $usersRepository;
    $this->passwords = $passwords;
    $this->userFormFactory = $userFormFactory;
    $this->modalRemoveFormFactory = $modalRemoveFormFactory;
    $this->passwordFormFactory = $passwordFormFactory;
    $this->editUserFormFactory = $editUserFormFactory;
  }

  /**
   * @throws AbortException
   */
  public function actionAll(): void
  {
    $this->userIsLogged();
    $this['breadcrumb']->add('Používatelia');
  }

  public function renderAll(): void
  {
    $this->template->users = $this->usersRepository->findAll();
  }

  /**
   * @param $id
   * @throws AbortException
   * @throws BadRequestException
   * @throws InvalidLinkException
   */
  public function actionView(int $id): void
  {
    $this->userIsLogged();
    $this->userRow = $this->usersRepository->findById($id);

    if (!$this->userRow) {
      throw new BadRequestException(self::ITEM_NOT_FOUND);
    }

    $this['editForm']->setDefaults($this->userRow);
    $this['breadcrumb']->add('Používatelia', $this->link('all'));
    $this['breadcrumb']->add($this->userRow->username);
  }

  /**
   * @param $id
   * @throws BadRequestException
   * @throws ForbiddenRequestException
   */
  public function renderView(int $id): void
  {
    if (!$this->userRow) {
      throw new BadRequestException(self::ITEM_NOT_FOUND);
    }
    $this->userIsAllowed($this->userRow->id, $this->user->roles[0], self::ROOT);
    $this->template->displayedUser = $this->userRow;
  }

  /**
   * @return Form
   */
  protected function createComponentUserForm(): Form
  {
    return $this->userFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->userIsLogged();
      $this->usersRepository->insert($values);
      $this->flashMessage(self::USER_ADDED, self::SUCCESS);
      $this->redirect('all');
    });
  }

  /**
   * @return Form
   */
  protected function createComponentEditForm(): Form
  {
    return $this->editUserFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->userIsLogged();
      $this->userRow->update($values);
      $this->flashMessage(self::ITEM_UPDATED, self::SUCCESS);
      $this->redirect('view', $this->userRow->id);
    });
  }

  /**
   * @return Form
   */
  protected function createComponentRemoveForm(): Form
  {
    return $this->modalRemoveFormFactory->create(function () {
      $this->userIsLogged();
      $this->submittedRemoveForm();
    });
  }

  /**
   * @return Form
   */
  protected function createComponentPasswordForm(): Form
  {
    return $this->passwordFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->userIsLogged();
      $this->submittedPassworddForm($values);
    });
  }

  /**
   * @param ArrayHash $values
   * @throws AbortException
   * @throws ForbiddenRequestException
   */
  private function submittedPassworddForm(ArrayHash $values): void
  {
    $this->userIsAllowed($this->userRow->id, $this->user->roles[0], self::ROOT);
    $this->userRow->update(array('password' => $this->passwords->hash($values->password)));
    $this->flashMessage('Heslo bolo zmenené', self::SUCCESS);
    $this->redirect('view', $this->userRow->id);
  }

  /**
   * @throws AbortException
   * @throws ForbiddenRequestException
   */
  private function submittedRemoveForm() {
    $this->userIsAllowed($this->userRow->id, $this->user->roles[0], self::ROOT);

    if ($this->userRow->id === $this->user->id) {
      $this->flashMessage('Nemožno odstrániť práve prihláseného používateľa', self::ERROR);
      $this->redirect('all');
    }

    $this->flashMessage(self::ITEM_REMOVED, self::SUCCESS);
    $this->usersRepository->softDelete((int)$this->userRow->id);
    $this->redirect('all');
  }

}
