<?php

namespace App\Presenters;

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

  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              UsersRepository $usersRepository,
                              Passwords $passwords)
  {
    parent::__construct($albumsRepository, $sectionRepository);
    $this->usersRepository = $usersRepository;
    $this->passwords = $passwords;
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
  public function actionShow($id) {
    $this->userIsLogged();
    $this->userRow = $this->usersRepository->findById($id);
  }

  /**
   * @param $id
   * @throws BadRequestException
   * @throws ForbiddenRequestException
   */
  public function renderShow($id) {
    if (!$this->userRow) {
      throw new BadRequestException($this->error);
    }
    $this->userIsAllowed($this->userRow->id, $this->user->roles[0], $this->root, $this->forbidden);
    $this->template->users = $this->userRow;
  }

  /**
   * @return Form
   */
  protected function createComponentAddForm() {
    $form = new Form;

    $form->addText('username', 'Používateľské meno')
         ->addRule(Form::FILLED, 'Používateľské meno musí byť vyplnené.')
         ->addRule(Form::MAX_LENGTH, 'Používateľské meno môže mať maximálne 50 znakov.', 50);

    $form->addPassword('password', 'Heslo')
         ->addRule(Form::FILLED, 'Heslo musí byť vyplnené.')
         ->addRule(Form::MAX_LENGTH, 'Heslo môže mať maximálne 50 znakov.', 50);

    $form->addSubmit('save', 'Uložiť');
    $form->onSuccess[] = [$this, 'submittedAddForm'];

    FormHelper::setBootstrapFormRenderer($form);
    return $form;
    }

  /**
   * @return Form
   */
  protected function createComponentEditForm() {
    $form = new Form;

    $form->addText('username', 'Používateľské meno')
         ->addRule(Form::FILLED, 'Meno musí byť vyplnené.')
         ->addRule(Form::MAX_LENGTH, 'Meno môže mať maximálne 50 znakov.', 50);

    $form->addSubmit('save', 'Uložiť')
         ->onClick[] = [$this, 'submittedEditForm'];

    $form->addSubmit('cancel', 'Zrušiť')
         ->setHtmlAttribute('class', 'btn btn-warning')
         ->onClick[] = [$this, 'formCancelled'];

    FormHelper::setBootstrapFormRenderer($form);
    return $form;
  }

  /**
   * @param SubmitButton $btn
   * @throws AbortException
   */
  public function submittedEditForm(SubmitButton $btn) {
    $values = $btn->form->getValues();
    $this->userRow->update($values);
    $this->redirect('all');
  }

  /**
   * @param Form $form
   * @param ArrayHash $values
   * @throws AbortException
   */
  public function submittedAddForm(Form $form, ArrayHash $values) {
    $this->userIsLogged();
    $this->usersRepository->insert($values);
    $this->redirect('all');
  }

  /**
   * @return Form
   */
  protected function createComponentPasswdForm() {
    $form = new Form;
    $form->addPassword('password', 'Heslo')
            ->addRule(Form::FILLED, 'Heslo musí byť vyplnené.')
            ->addRule(Form::MAX_LENGTH, 'Heslo môže mať maximálne 100 znakov.', 100)
            ->addRule(Form::MIN_LENGTH, 'Heslo musí mať minimálne 5 znakov.', 5);

    $form->addPassword('password_again', 'Heslo znovu')
            ->addRule(Form::FILLED, 'Heslo znovu musí byť vyplnené.')
            ->addRule(Form::EQUAL, 'Heslá sa nezhodujú.', $form['password']);

    $form->addSubmit('save', 'Nastaviť');
    $form->addProtection('Vypršal časový limit, odošli formulár znovu.');

    $form->onSuccess[] = [$this, 'submittedPasswdForm'];
    FormHelper::setBootstrapRenderer($form);
    return $form;
  }

  /**
   * @param Form $form
   * @throws AbortException
   * @throws ForbiddenRequestException
   */
  public function submittedPasswdForm(Form $form, ArrayHash $values) {
    $this->userIsAllowed($this->userRow->id, $this->user->roles[0], $this->root, $this->forbidden);
    $this->userRow->update(array('password' => $this->passwords->hash($values->password)));
    $this->redirect('all');
  }

  /**
   * @throws AbortException
   * @throws ForbiddenRequestException
   */
  public function submittedRemoveForm() {
    $this->userIsLogged();
    $this->userIsAllowed($this->userRow->id, $this->user->roles[0], $this->root, $this->forbidden);
    $this->usersRepository->softDelete($this->userRow->id);
    $this->redirect('all#primary');
  }

}
