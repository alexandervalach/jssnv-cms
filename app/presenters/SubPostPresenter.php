<?php

namespace App\Presenters;

use App\FormHelper;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;

class SubPostPresenter extends BasePresenter {

    /** @var ActiveRow */
    private $subPostRow;

    /** @var ActiveRow */
    private $subSectionRow;
    
    /** @var string */
    private $storage = 'files/';

    /** @var string */
    private $error = "Post not found!";
    
    public function actionShow($id) {
        $this->subPostRow = $this->subPostRepository->findByValue('subsection_id', $id)->fetch();
    }

    public function renderShow($id) {
        $this->template->subPost = $this->subPostRow;
    }

    public function actionEdit($id) {
        $this->userIsLogged();
        $this->subPostRow = $this->subPostRepository->findById($id);
        $this->subSectionRow = $this->subPostRow->ref('subsection_id');
    }

    public function renderEdit($id) {
        if (!$this->subPostRow) {
            throw new BadRequestException($this->error);
        }
        $this->template->subPost = $this->subPostRow;
        $this->getComponent('editForm')->setDefaults($this->subPostRow);
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
        if ($this->subSectionRow->name != $values['name']) {
            $this->subSectionRow->update(array('name' => $values['name']));
        }
        $this->subPostRow->update($values);
        $this->redirect('show', $this->subPostRow->subsection_id);
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
        $subSectionRow = $this->subPostRow->ref('subsection', 'subsection_id');
        $subSectionRow->delete();
        $this->subPostRow->delete();
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
                $file->move($file->storage . $name);

                $fileData['name'] = $name;
                $fileData['post_id'] = $this->postRow;
                $this->fileRepository->insert($fileData);
            }
        }
        $this->redirect('view#primary', $this->postRow);
    }

}
