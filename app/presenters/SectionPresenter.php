<?php

namespace App\Presenters;

use App\FormHelper;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;
use Nette\Forms\Controls\SubmitButton;

class SectionPresenter extends BasePresenter {

    /** @var ActiveRow */
    private $sectionRow;

    /** @var string */
    private $error = "Section not found!";

    public function actionAll() {
        $this->userIsLogged();
    }

    public function renderAll() {
        $this->template->listOfSections = $this->sectionRepository->findAll()->order("order DESC");
    }

    public function actionEdit($id) {
        $this->sectionRow = $this->sectionRepository->findById($id);
    }

    public function renderEdit($id) {
        if (!$this->sectionRow) {
            throw new BadRequestException($this->error);
        }
        $this->template->section = $this->sectionRow;
        $this->getComponent('editForm')->setDefaults($this->sectionRow);
    }

    public function actionAdd() {
        $this->userIsLogged();
    }

    public function renderAdd() {
        $this->getComponent('addForm');
    }

    public function actionRemove($id) {
        $this->userIsLogged();
        $this->sectionRow = $this->sectionRepository->findById($id);
    }

    public function renderRemove($id) {
        if (!$this->sectionRow) {
            throw new BadRequestException($this->error);
        }
        $this->template->section = $this->sectionRow;
        $this->getComponent('removeForm');
    }

    protected function createComponentAddForm() {
        $form = new Form;
        $form->addText('name', 'Názov')
                ->setRequired('Názov musí byť vyplnený.')
                ->addRule(Form::MAX_LENGTH, 'Názov môže mať maximálne 50 znakov.', 50);
        $form->addText('url', 'URL adresa')
                ->addRule(Form::MAX_LENGTH, 'URL môže mať maximálne 200 znakov.', 200);
        $form->addCheckbox('homeUrl', ' URL na tejto stránke')
                ->setDefaultValue(0);
        $form->addText('order', 'Poradie')
                ->setDefaultValue(0)
                ->addRule(Form::INTEGER, 'Poradie môže byť len celé číslo.');
        $form->addCheckbox('visible', ' Viditeľné v bočnom menu')
                ->setDefaultValue(1);
        $form->addCheckbox('sliding', ' Rolovacie menu');
        $form->addSubmit('save', 'Zapísať');

        $form->onSuccess[] = $this->submittedAddForm;
        FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    protected function createComponentEditForm() {
        $form = new Form;
        $form->addText('name', 'Názov')
                ->setRequired('Názov musí byť vyplnený.')
                ->addRule(Form::MAX_LENGTH, 'Názov môže mať maximálne 50 znakov.', 50);
        $form->addText('url', 'URL adresa')
                ->addRule(Form::MAX_LENGTH, 'URL môže mať maximálne 200 znakov.', 200);
        $form->addCheckbox('homeUrl', ' URL na tejto stránke')
                ->setDefaultValue(0);
        $form->addText('order', 'Poradie')
                ->addRule(Form::INTEGER, 'Poradie môže byť len celé číslo.');
        $form->addCheckbox('visible', ' Viditeľné v bočnom menu')
                ->setDefaultValue(1);
        $form->addCheckbox('sliding', ' Rolovacie menu');
        $form->addSubmit('save', 'Zapísať')
                ->onClick[] = $this->submittedEditForm;
        $form->addSubmit('cancel', 'Zrušiť')
                        ->setAttribute('class', 'btn btn-warning')
                ->onClick[] = $this->formCancelled;

        FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    protected function createComponentRemoveForm() {
        $form = new Form;
        $form->addSubmit('remove', 'Odstrániť')
                        ->setAttribute('class', 'btn btn-danger')
                ->onClick[] = $this->submittedRemoveForm;
        $form->addSubmit('cancel', 'Zrušiť')
                        ->setAttribute('class', 'btn btn-warning')
                ->onClick[] = $this->formCancelled;
        return $form;
    }

    public function submittedAddForm(Form $form) {
        $values = $form->getValues();
        $id = $this->sectionRepository->insert($values);

        if (empty($values->url)) {
            $postData = array(
                'section_id' => $id,
                'name' => $values['name']
            );
            $this->postRepository->insert($postData);
            $this->redirect('Post:show#primary', $id);
        } else {
            $this->redirect('Homepage:#primary');
        }
    }

    public function submittedEditForm(SubmitButton $btn) {
        $values = $btn->form->getValues();
        $this->sectionRow->update($values);
        $this->redirect('Section:all#primary');
    }

    public function submittedRemoveForm() {
        if ($this->sectionRow->url == NULL || $this->sectionRow->url == "") {
            $post = $this->sectionRow->related('post')->fetch();
            $post->delete();
        }
        $this->sectionRow->delete();
        $this->redirect('all#primary');
    }

    public function formCancelled() {
        $this->redirect('all#primary');
    }

}
