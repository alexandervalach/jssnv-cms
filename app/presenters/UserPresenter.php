<?php

namespace App\Presenters;

use App\FormHelper;
use App\Model\UserRepository;
use Nette\Application\UI\Form;
use Nette\Application\BadRequestException;
use Nette\Database\Table\ActiveRow;

class UserPresenter extends BasePresenter {

    /** @var ActiveRow */
    private $userRow;

    /** @var string */
    private $error = "User not found!";

    public function actionAll() {
        $this->userIsLogged();
    }

    public function renderAll() {
        $this->template->users = $this->userRepository->findAll();
    }

    public function actionAdd() {
        $this->userIsLogged();
    }

    public function renderAdd() {
        $this->getComponent('addForm');
    }

    public function actionEdit($id) {
        $this->userIsLogged();
        $this->userRow = $this->userRepository->findById($id);
    }

    public function renderEdit($id) {
        if (!$this->userRow) {
            throw new BadRequestException($this->error);
        }
        $this->template->users = $this->userRow;
        $this->getComponent('editForm')->setDefaults($this->userRow);
    }

    public function actionPasswd($id) {
        $this->userIsLogged();
        $this->userRow = $this->userRepository->findById($id);
    }

    public function renderPasswd($id) {
        if (!$this->userRow) {
            throw new BadRequestException($this->error);
        }
        $this->getComponent('passwdForm');
    }

    public function actionShow($id) {
        $this->userIsLogged();
        $this->userRow = $this->userRepository->findById($id);
    }

    public function renderShow($id) {
        if (!$this->userRow) {
            throw new BadRequestException($this->error);
        }
        $this->template->users = $this->userRow;
    }

    protected function createComponentAddForm() {
        $form = new Form;
        $form->addText('username', 'Užívateľské meno')
                ->addRule(Form::FILLED, 'Užívateľské meno musí byť vyplnené.')
                ->addRule(Form::MAX_LENGTH, 'Užívateľské meno môže mať maximálne 50 znakov.', 50);
        $form->addSelect('role', 'Úloha', UserRepository::$ROLES);
        $form->addSubmit('save', 'Zapísať');
        $form->onSuccess[] = $this->submittedAddForm;
        FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    protected function createComponentEditForm() {
        $form = new Form;
        $form->addText('username', 'Užívateľské meno')
                ->addRule(Form::FILLED, 'Užívateľské meno musí byť vyplnené.')
                ->addRule(Form::MAX_LENGTH, 'Užívateľské meno môže mať maximálne 50 znakov.', 50);
        $form->addSubmit('save', 'Zapísať');
        $form->onSuccess[] = $this->submittedEditForm;
        FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    public function submittedEditForm(Form $form) {
        $values = $form->getValues();
        $this->userRow->update($values);
        $this->redirect('all');
    }

    public function submittedAddForm(Form $form) {
        $values = $form->getValues();
        $this->userRepository->insert($values);
        $this->redirect('all');
    }

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

        $form->onSuccess[] = $this->submittedPasswdForm;
        FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    public function submittedPasswdForm(Form $form) {
        $values = $form->getValues();
        $this->userRow->update(array('password' => md5($values['password'])));
        $this->redirect('all');
    }
    
    public function submittedRemoveForm() {
        $this->userIsLogged();
        $this->userRow->delete();
        $this->redirect('all#primary');
    }

}
