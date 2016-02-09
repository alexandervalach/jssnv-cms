<?php

namespace App\Presenters;

use App\FormHelper;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Database\Table\ActiveRow;

class SubSectionPresenter extends BasePresenter {

    /** @var ActiveRow */
    private $subSectionRow;

    /** @var string */
    private $error = "Subsection not found!";

    public function actionAll() {
        $this->userIsLogged();
    }

    public function renderAll() {
        $this->template->listOfSubSections = $this->subSectionRepository->findAll()->order('section_id DESC');
    }

    public function actionEdit($id) {
        $this->subSectionRow = $this->subSectionRepository->findById($id);
    }

    public function renderEdit($id) {
        if (!$this->subSectionRow) {
            throw new BadRequestException($this->error);
        }
        $this->template->subSection = $this->subSectionRow;
        $this->getComponent('editForm')->setDefaults($this->subSectionRow);
    }

    public function actionAdd() {
        $this->userIsLogged();
    }

    public function renderAdd() {
        $this->getComponent('addForm');
    }

    public function actionRemove($id) {
        $this->userIsLogged();
        $this->subSectionRow = $this->subSectionRepository->findById($id);
    }

    public function renderRemove($id) {
        if (!$this->subSectionRow) {
            throw new BadRequestException($this->error);
        }
        $this->template->subSection = $this->subSectionRow;
        $this->getComponent('removeForm');
    }

    protected function createComponentAddForm() {
        $form = new Form;
        $sections = $this->sectionRepository->getSections();
        $form->addSelect('section_id', 'Vyberte sekciu', $sections);
        $form->addText('name', 'Názov')
                ->setRequired('Názov musí byť vyplnený.')
                ->addRule(Form::MAX_LENGTH, 'Názov môže mať maximálne 50 znakov.', 50);
        $form->addText('url', 'URL adresa')
                ->addRule(Form::MAX_LENGTH, 'URL môže mať maximálne 200 znakov.', 200);
        $form->addCheckbox('homeUrl', ' URL na tejto stránke');
        $form->addText('order', 'Poradie')
                ->setDefaultValue(5)
                ->addRule(Form::INTEGER, 'Poradie môže byť len celé číslo.');
        $form->addSubmit('save', 'Zapísať');

        $form->onSuccess[] = $this->submittedAddForm;
        FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    public function submittedAddForm(Form $form) {
        $values = $form->getValues();
        $id = $this->subSectionRepository->insert($values);
        if (empty($values->url)) {
            $postData = array(
                'subsection_id' => $id,
                'name' => $values['name']
            );
            $this->subPostRepository->insert($postData);
            $this->redirect('SubPost:show', $id);
        }
        $this->redirect('all#primary');
    }

    protected function createComponentEditForm() {
        $form = new Form;
        $form->addText('name', 'Názov')
                ->setRequired('Názov musí byť vyplnený.')
                ->addRule(Form::MAX_LENGTH, 'Názov môže mať maximálne 50 znakov.', 50);
        $form->addText('url', 'URL adresa')
                ->addRule(Form::MAX_LENGTH, 'URL môže mať maximálne 200 znakov.', 200);
        $form->addCheckbox('homeUrl', ' URL na tejto stránke');
        $form->addText('order', 'Poradie')
                ->addRule(Form::INTEGER, 'Poradie môže byť len celé číslo.');
        $form->addSubmit('save', 'Zapísať')
                ->onClick[] = $this->submittedEditForm;
        $form->addSubmit('cancel', 'Zrušiť')
                        ->setAttribute('class', 'btn btn-warning')
                ->onClick[] = $this->formCancelled;

        $form->onSuccess[] = $this->submittedEditForm;
        FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    protected function createComponentRemoveForm() {
        $form = new Form;
        $form->addSubmit('cancel', 'Zrušiť')
                        ->setAttribute('class', 'btn btn-warning')
                ->onClick[] = $this->formCancelled;
        $form->addSubmit('remove', 'Odstrániť')
                        ->setAttribute('class', 'btn btn-danger')
                ->onClick[] = $this->submittedRemoveForm;
        return $form;
    }

    public function submittedEditForm(SubmitButton $btn) {
        $values = $btn->form->getValues();
        $this->subSectionRow->update($values);
        $this->redirect('SubPost:show', $this->subSectionRow);
    }

    public function submittedRemoveForm() {
        if ($this->subSectionRow->url == NULL || $this->subSectionRow->url == "") {
            $subPost = $this->subSectionRow->related('post')->fetch();
            $subPost->delete();
        }
        $this->subSectionRow->delete();
        $this->redirect('all#primary');
    }

    public function formCancelled() {
        $this->redirect('all#primary');
    }

}
