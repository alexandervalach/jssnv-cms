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

        if (!$this->subFileRow) {
            throw new BadRequestException($this->error);
        }
    }

    public function renderRemove($id) {
        $this->template->file = $this->subFileRow;
    }

    protected function createComponentRemoveFileForm() {
        $form = new Form;
        
        $form->addSubmit('cancel', 'Zrušiť')
                        ->setAttribute('class', 'btn btn-warning')
                ->onClick[] = [$this, 'formCancelled'];

        $form->addSubmit('remove', 'Odstrániť')
                        ->setAttribute('class', 'btn btn-danger')
                ->onClick[] = [$this, 'submittedFileRemoveForm'];
        
        return $form;
    }

    public function formCancelled() {
        $id = $this->subFileRow->ref('subpost', 'subpost_id');
        $this->redirect('SubPost:show#primary', $id);
    }

    public function submittedFileRemoveForm() {
        $this->userIsLogged();
        $id = $this->subFileRow->ref('subpost', 'subpost_id');
        $this->subFileRow->delete();
        $this->redirect('SubPost:show#primary', $id);
    }

}
