<?php

namespace App\Presenters;

use App\FormHelper;
use Nette\Database\Table\ActiveRow;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;

class PostPresenter extends BasePresenter {

    /** @var ActiveRow */
    private $postRow;

    /** @var ActiveRow */
    private $sectionRow;

    /** @var string */
    private $error = "Post not found!";

    public function actionShow($id) {
        $this->sectionRow = $this->sectionRepository->findById($id);
        $this->postRow = $this->sectionRow->related('post')->fetch();
    }

    public function renderShow() {
        $this->template->section = $this->sectionRow;
        $this->template->post = $this->postRow;
    }

    public function actionEdit($id) {
        $this->userIsLogged();
        $this->sectionRow = $this->sectionRepository->findById($id);
        $this->postRow = $this->sectionRow->related('post')->fetch();
    }

    public function renderEdit($id) {
        $this->sectionRow;
        if (!$this->sectionRow) {
            throw new BadRequestException($this->error);
        }
        if (!$this->postRow) {
            throw new BadRequestException($this->error);
        }
        $this->template->post = $this->postRow;
        $this->template->section = $this->sectionRow;
        $this->getComponent('editForm')->setDefaults($this->postRow);
    }

    protected function createComponentEditForm() {
        $form = new Form;

        $form->addTextArea('content', 'Obsah:')
                ->setAttribute('class', 'form-jqte')
                ->setRequired("Obsah príspevku je povinné pole.");

        $form->addSubmit('save', 'Uložiť');

        $form->onSuccess[] = $this->submittedEditForm;
        FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    public function submittedEditForm(Form $form) {
        $this->userIsLogged();
        $values = $form->getValues();
        $this->postRow->update($values);
        $this->redirect('show', $this->postRow->id);
    }

    /*
      public function formCancelled() {
      $this->redirect('Homepage:');
      }
     */
}
