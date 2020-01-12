<?php

namespace App\Presenters;

use App\Forms\ModalRemoveFormFactory;
use App\Forms\PasswordFormFactory;
use App\Forms\RemoveFormFactory;
use App\Forms\UserFormFactory;
use App\Helpers\FormHelper;
use App\Model\AlbumsRepository;
use App\Model\SectionsRepository;
use App\Model\UsersRepository;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;
use Nette\Forms\Controls\SubmitButton;
use Nette\Security\Passwords;
use Nette\Utils\ArrayHash;

/**
 * Class UserPresenter
 * @package App\Presenters
 */
class UsersPresenter extends BasePresenter {

  /** @var ActiveRow */
  private $userRow;

  /** @var string */
  private $error = "User not found!";

  /** @var string */
  private $forbidden = "Action not allowed!";

  /** @var string */
  private $root = "admin";

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

  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              UsersRepository $usersRepository,
                              Passwords $passwords,
                              UserFormFactory $userFormFactory,
                              ModalRemoveFormFactory $modalRemoveFormFactory,
                              PasswordFormFactory $passwordFormFactory)
  {
    parent::__construct($albumsRepository, $sectionRepository);
    $this->usersRepository = $usersRepository;
    $this->passwords = $passwords;
    $this->userFormFactory = $userFormFactory;
    $this->modalRemoveFormFactory = $modalRemoveFormFactory;
    $this->passwordFormFactory = $passwordFormFactory;
  }

  /**
   * @throws AbortException
   */
  public function actionAll() {
    $this->userIsLogged();
  }

  /**
   *
   */
  public function renderAll() {
    $this->template->users = $this->usersRepository->findAll();
  }

  /**
   * @throws AbortException
   */
  public function actionAdd() {
    $this->userIsLogged();
  }

  /**
   * @param $id
   * @throws BadRequestException
   * @throws AbortException
   */
  public function actionEdit($id) {
    $this->userIsLogged();
    $this->userRow = $this->usersRepository->findById($id);

    if (!$this->userRow) {
      throw new BadRequestException($this->error);
    }
  }

  /**
   * @param $id
   * @throws ForbiddenRequestException
   */
  public function renderEdit($id) {
    $this->userIsAllowed($this->userRow->id, $this->user->roles[0], $this->root, $this->forbidden);
    $this->template->users = $this->userRow;
    $this->getComponent('editForm')->setDefaults($this->userRow);
  }

  /**
   * @param $id
   * @throws AbortException
   */
  public function actionPasswd($id) {
    $this->userIsLogged();
    $this->userRow = $this->usersRepository->findById($id);
  }

  /**
   * @param $id
   * @throws BadRequestException
   * @throws ForbiddenRequestException
   */
  public function renderPasswd($id) {
    if (!$this->userRow) {
      throw new BadRequestException($this->error);
    }
    $this->userIsAllowed($this->userRow->id, $this->user->roles[0], $this->root, $this->forbidden);
    $this->template->shownUser = $this->userRow;
   }

  /**
   * @param $id
   * @throws AbortException
   */
  public function actionView($id) {
    $this->userIsLogged();
    $this->userRow = $this->usersRepository->findById($id);
    $this['editForm']->setDefaults($this->userRow);
  }

  /**
   * @param $id
   * @throws BadRequestException
   * @throws ForbiddenRequestException
   */
  public function renderView($id) {
    if (!$this->userRow) {
      throw new BadRequestException($this->error);
    }
    $this->userIsAllowed($this->userRow->id, $this->user->roles[0], $this->root, $this->forbidden);
    $this->template->users = $this->userRow;
  }

  /**
   * @return Form
   */
  protected function createComponentUserForm() {
    return $this->userFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->userIsLogged();
      $this->submittedAddForm($values);
    });
  }

  /**
   * @return Form
   */
  protected function createComponentEditForm() {
    $form = new Form;

    $form->addText('username', 'Používateľské meno*')
         ->addRule(Form::FILLED, 'Meno musí byť vyplnené.')
         ->addRule(Form::MAX_LENGTH, 'Meno môže mať maximálne 50 znakov.', 50);
    $form->addSubmit('save', 'Uložiť');
    $form->addSubmit('cancel', 'Zrušiť')
         ->setHtmlAttribute('class', 'btn btn-warning')
         ->setHtmlAttribute('data-dismiss', 'modal');
    $form->onSuccess[] = [$this, 'submittedEditForm'];

    FormHelper::setBootstrapFormRenderer($form);
    return $form;
  }

  /**
   * @return Form
   */
  protected function createComponentRemoveForm(): Form
  {
    return $this->modalRemoveFormFactory->create(function () {
      $this->submittedRemoveForm();
    });
  }

  /**
   * @param ArrayHash $values
   * @throws AbortException
   */
  public function submittedEditForm(Form $form, ArrayHash $values): void
  {
    $this->userIsLogged();
    $this->userRow->update($values);
    $this->flashMessage(self::ITEM_UPDATED, self::SUCCESS);
    $this->redirect('view', $this->userRow->id);
  }

  /**
   * @param ArrayHash $values
   * @throws AbortException
   */
  private function submittedAddForm(ArrayHash $values): void
  {
    $this->userIsLogged();
    $this->usersRepository->insert($values);
    $this->redirect('all');
  }

  /**
   * @return Form
   */
  protected function createComponentPasswordForm(): Form
  {
    return $this->passwordFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->submittedPassworddForm($values);
    });
  }

  /**
   * @param Form $form
   * @throws AbortException
   * @throws ForbiddenRequestException
   */
  public function submittedPassworddForm(ArrayHash $values): void
  {
    $this->userIsAllowed($this->userRow->id, $this->user->roles[0], $this->root, $this->forbidden);
    $this->userRow->update(array('password' => $this->passwords->hash($values->password)));
    $this->flashMessage('Heslo bolo zmenené', self::SUCCESS);
    $this->redirect('view', $this->userRow->id);
  }

  /**
   * @throws AbortException
   * @throws ForbiddenRequestException
   */
  private function submittedRemoveForm() {
    $this->userIsLogged();
    $this->userIsAllowed($this->userRow->id, $this->user->roles[0], $this->root, $this->forbidden);

    if ($this->userRow->id === $this->user->id) {
      $this->flashMessage('Nemožno odstrániť práve prihláseného používateľa', self::ERROR);
      $this->redirect('all');
    }

    $this->flashMessage(self::ITEM_REMOVED, self::SUCCESS);
    $this->usersRepository->softDelete($this->userRow->id);
    $this->redirect('all');
  }

}
