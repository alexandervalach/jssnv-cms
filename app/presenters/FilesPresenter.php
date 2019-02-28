<?php

namespace App\Presenters;

use App\FormHelper;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Utils\FileSystem;

class FilesPresenter extends BasePresenter {

    /** @var ActiveRow */
    private $fileRow;

    /** @var string */
    private $error = "File not found!";

    public function actionAll() {
        $this->userIsLogged();
    }

    public function renderAll() {
        $this->template->files = $this->filesRepository->findAll()->order('name ASC');
        $this->template->subFiles = $this->subFilesRepository->findAll()->order('name ASC');
        $this->template->fileFolder = $this->fileFolder;
    }

    public function actionRemove($id) {
        $this->userIsLogged();
        $this->fileRow = $this->filesRepository->findById($id);

        if (!$this->fileRow) {
            throw new BadRequestException($this->error);
        }
    }

    public function renderRemove($id) {
        $this->template->file = $this->fileRow;
    }

    protected function createComponentRemoveFileForm() {
        $form = new Form;
        
        $form->addSubmit('cancel', 'Zrušiť')
                        ->setAttribute('class', 'btn btn-warning')
                ->onClick[] = [$this, 'formCancelled'];

        $form->addSubmit('remove', 'Odstrániť')
                        ->setAttribute('class', 'btn btn-danger')
                ->onClick[] = [$this, 'submittedFileRemoveForm'];

        FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    public function formCancelled() {
        $this->redirect('Post:show#primary', $this->fileRow->ref('post', 'post_id'));
    }

    public function submittedFileRemoveForm() {
        $this->userIsLogged();
        $id = $this->fileRow->ref('post', 'post_id');
        $file->delete();
        $this->redirect('Post:show#primary', $id);
    }

}
