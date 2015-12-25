<?php

namespace App\Presenters;

use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Utils\FileSystem;

class SubFilesPresenter extends BasePresenter {

    /** @var ActiveRow */
    private $subFileRow;

    /** @var string */
    private $error = "File not found!";

    public function actionRemove($id) {
        $this->subFileRow = $this->subFilesRepository->findById($id);
    }

    public function renderRemove($id) {
        if (!$this->subFileRow) {
            throw new BadRequestException($this->error);
        }
        $this->template->file = $this->subFileRow;
    }

    protected function createComponentRemoveFileForm() {
        $form = new Form;
        $form->addSubmit('remove', 'Odstrániť')
                        ->setAttribute('class', 'btn btn-danger')
                ->onClick[] = $this->submittedFileRemoveForm;
        $form->addSubmit('cancel', 'Zrušiť')
                        ->setAttribute('class', 'btn btn-warning')
                ->onClick[] = $this->formCancelled;
        return $form;
    }

    public function formCancelled() {
        $this->redirect('SubPost:show#primary', $this->subFileRow->ref('subpost', 'subpost_id'));
    }

    public function submittedFileRemoveForm() {
        $file = $this->subFileRow;
        $fileFile = new FileSystem;
        $fileFile->delete($this->fileFolder . $file->name);
        $id = $file->ref('subpost', 'subpost_id');
        $file->delete();
        $this->redirect('SubPost:show#primary', $id);
    }

}
