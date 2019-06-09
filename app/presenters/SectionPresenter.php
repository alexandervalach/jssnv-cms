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

        if (!$this->sectionRow) {
            throw new BadRequestException($this->error);
        }
    }

    public function renderEdit($id) {
        $this->template->mainSection = $this->sectionRow;

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

        if (!$this->sectionRow) {
            throw new BadRequestException($this->error);
        }
    }

    public function renderRemove($id) {
        $this->template->mainSection = $this->sectionRow;
        $this->getComponent('removeForm');
    }

    protected function createComponentAddForm() {
        $form = new Form;
        
        $form->addText('name', 'Názov')
                ->setRequired('Názov musí byť vyplnený.')
                ->addRule(Form::MAX_LENGTH, 'Názov môže mať maximálne 50 znakov.', 50);
        
        $form->addText('url', 'URL adresa');
        
        $form->addCheckbox('homeUrl', ' URL na tejto stránke')
                ->setDefaultValue(0);
        
        $form->addText('order', 'Poradie')
                ->setRequired(true)
                ->setDefaultValue(50)
                ->addRule(Form::INTEGER, 'Poradie môže byť len celé číslo.');
        
        $form->addCheckbox('visible', ' Viditeľné v bočnom menu')
                ->setDefaultValue(1);

        $form->addCheckbox('sliding', ' Rolovacie menu');

        $form->addSubmit('save', 'Uložiť');

        $form->onSuccess[] = [$this, 'submittedAddForm'];
        FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    protected function createComponentEditForm() {
        $form = new Form;
        
        $form->addText('name', 'Názov')
                ->setRequired('Názov musí byť vyplnený.')
                ->addRule(Form::MAX_LENGTH, 'Názov môže mať maximálne 50 znakov.', 50);
        
        $form->addText('url', 'URL adresa');
        
        $form->addCheckbox('homeUrl', ' URL na tejto stránke');
        
        $form->addText('order', 'Poradie')
                ->setRequired('Poradie musí byť vyplnené')
                ->addRule(Form::INTEGER, 'Poradie môže byť len celé číslo.');
        
        $form->addCheckbox('visible', ' Viditeľné v bočnom menu');
        
        $form->addCheckbox('sliding', ' Rolovacie menu');

        $form->addSubmit('save', 'Uložiť')
                ->onClick[] = [$this, 'submittedEditForm'];
        
        $form->addSubmit('cancel', 'Zrušiť')
                        ->setAttribute('class', 'btn btn-warning')
                ->onClick[] = [$this, 'formCancelled'];

        FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    protected function createComponentRemoveForm() {
        $form = new Form;
        
        $form->addSubmit('cancel', 'Zrušiť')
                        ->setAttribute('class', 'btn btn-warning')
                ->onClick[] = [$this, 'formCancelled'];
        
        $form->addSubmit('remove', 'Odstrániť')
                        ->setAttribute('class', 'btn btn-danger')
                ->onClick[] = [$this, 'submittedRemoveForm'];

        FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    public function submittedAddForm(Form $form) {
        $this->userIsLogged();

        $values = $form->getValues();
        $id = $this->sectionRepository->insert($values);

        if (empty($values->url)) {
            
            $postData = array(
                'section_id' => $id,
                'name' => $values['name']
            );
            $this->postRepository->insert($postData);
            $this->flashMessage('Sekcia bola pridaná');
            $this->redirect('Post:show#primary', $id);

        } else {
            
            $this->flashMessage('Sekcia bola pridaná');
            $this->redirect('all#primary');
        
        }
    }

    public function submittedEditForm(SubmitButton $btn) {
        $this->userIsLogged();

        $values = $btn->form->getValues();
        $this->sectionRow->update($values);
        $this->flashMessage('Sekcia bola upravená');
        $this->redirect('all#primary');
    }

    public function submittedRemoveForm() {
        $this->userIsLogged();

        if ($this->sectionRow->url == NULL || $this->sectionRow->url == "") {
            $post = $this->sectionRow->related('post')->fetch();
            $post->delete();
        }

        $this->sectionRow->delete();
        $this->flashMessage('Sekcia bola odstránená');
        $this->redirect('all#primary');
    }

    public function formCancelled() {
        $this->redirect('all#primary');
    }

}
