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
    protected $storage = "files/";

    /** @var string */
    private $error = "Post not found!";

    public function actionShow($id) {
        $this->postRow = $this->postRepository->findByValue('section_id', $id)->fetch();
    }

    public function renderShow() {
        $this->template->post = $this->postRow;
        $this->template->files = $this->filesRepository->findByValue('post_id', $this->postRow);
        $this->template->fileFolder = $this->storage;
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
        $form->addCheckbox('onHomepage', ' Na domovskej stránke');
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
            $this->sectionRow->update(array('name' => $values['name']));
        }
        $this->redirect('show', $this->postRow->section_id);
    }

    public function submittedRemoveForm(Form $form) {
        $subSectionRow = $this->postRow->ref('section', 'section_id');
        $subSectionRow->delete();
        $this->postRow->delete();
        $this->flashMessage('Sekcia bola odstránená.', 'alert-success');
        $this->redirect('Homepage:');
    }

    protected function createComponentUploadFilesForm() {
        $form = new Form;
        $form->addUpload('files', 'Vyber súbory', true);
        $form->addSubmit('upload', 'Nahraj');
        $form->onSuccess[] = $this->submittedUploadFilesForm;
        FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    public function submittedUploadFilesForm(Form $form) {
        $this->userIsLogged();
        $values = $form->getValues();
        $fileData = array();
        foreach ($values['files'] as $file) {
            $name = strtolower($file->getSanitizedName());

            if ($file->isOk()) {
                $file->move($this->storage . $name);

                $fileData['name'] = $name;
                $fileData['post_id'] = $this->postRow;
                $this->filesRepository->insert($fileData);
            }
        }
        $this->redirect('show#primary', $this->postRow);
    }

}
