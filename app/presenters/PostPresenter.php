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
        $this->postRow = $this->postRepository->findByValue('section_id', $id)->fetch();
    }

    public function renderShow() {
        $this->template->post = $this->postRow;
    }

    public function actionEdit($id) {
        $this->userIsLogged();
        $this->postRow = $this->postRepository->findById($id);
        $this->sectionRow = $this->postRow->ref('section_id');
    }

    public function renderEdit($id) {
        if (!$this->postRow) {
            throw new BadRequestException($this->error);
        }
        $this->template->post = $this->postRow;
        $this->getComponent('editForm')->setDefaults($this->postRow);
    }

    protected function createComponentEditForm() {
        $form = new Form;
        $form->addText('name', 'Názov');
        $form->addTextArea('content', 'Obsah:')
                ->setAttribute('id', 'ckeditor');
        $form->addSubmit('save', 'Uložiť');
        $form->onSuccess[] = $this->submittedEditForm;
        FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    public function submittedEditForm(Form $form) {
        $this->userIsLogged();
        $values = $form->getValues();
        $this->postRow->update($values);
        if ($this->sectionRow->name != $values['name']) {
            $this->sectionRow->update( array( 'name' => $values['name'] ) );
        }
        $this->redirect('show', $this->postRow->section_id);
    }

    protected function createComponentRemoveForm() {
        $form = new Form();
        $form->addSubmit('remove', 'Odstrániť')
                        ->getControlPrototype()->class = "btn btn-danger";
        $form->onSuccess[] = $this->submittedRemoveForm;
        FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    public function submittedRemoveForm(Form $form) {
        $subSectionRow = $this->postRow->ref('section', 'section_id');
        $subSectionRow->delete();
        $this->postRow->delete();
        $this->flashMessage('Sekcia bola odstránená.', 'alert-success');
        $this->redirect('Homepage:');
    }

}
