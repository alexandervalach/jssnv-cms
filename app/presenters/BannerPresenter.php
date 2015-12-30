<?php

namespace App\Presenters;

use App\FormHelper;
use Nette\Database\Table\ActiveRow;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;

class BannerPresenter extends BasePresenter {

    /** @var ActiveRow */
    private $bannerRow;

    /** @var string */
    private $error = "Item not found!";

    public function actionEdit($id) {
        $this->bannerRow = $this->bannerRepository->findById($id);
    }

    public function renderEdit($id) {
        if (!$this->bannerRow) {
            throw new BadRequestException($this->error);
        }
        $this->template->banner = $this->bannerRow;
        $this->getComponent('editForm')->setDefaults($this->bannerRow);
    }

    protected function createComponentEditForm() {
        $form = new Form;
        $form->addTextArea('message', 'Text:')
                ->addRule(Form::FILLED, 'Text muís byť vyplnený.')
                ->addRule(Form::MAX_LENGTH, 'Maximálna dĺžka textu je 250 znakov.', 250);
        $form->addText('link', 'Odkaz:')
                ->addRule(Form::MAX_LENGTH, 'Maximálna dĺžka odkazu je 250 znakov.', 250);
        $form->addSubmit('save', 'Zapísať')
                ->onClick[] = $this->submittedEditForm;
        $form->addSubmit('cancel', 'Zrušiť')
                ->setAttribute('class', 'btn btn-warning')
                ->onClick[] = $this->formCancelled;

        FormHelper::setBootstrapRenderer($form);
        return $form;
    }

    public function submittedEditForm(SubmitButton $btn) {
        $values = $btn->form->getValues();
        $this->bannerRow->update($values);
        $this->redirect('Homepage:#primary');
    }

    public function formCancelled() {
        $this->redirect('Homepage:#primary');
    }

}
