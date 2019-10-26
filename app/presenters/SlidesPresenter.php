<?php

namespace App\Presenters;

use App\FormHelper;
use Nette\Database\Table\ActiveRow;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;

/**
 * Class SlidesPresenter
 * @package App\Presenters
 */
class SlidesPresenter extends BasePresenter {

  /** @var ActiveRow */
  private $slideRow;

  /** @var string */
  private $error = "Item not found!";

  /**
   * @param $id
   * @throws BadRequestException
   */
  public function actionEdit($id) {
    $this->slideRow = $this->slidesRepository->findById($id);

    if (!$this->slideRow) {
      throw new BadRequestException($this->error);
    }
  }

  /**
   * @param $id
   */
  public function renderEdit($id) {
    $this->template->banner = $this->slideRow;
    $this['editForm']->setDefaults($this->slideRow);
  }

  /**
   * @return Form
   */
  protected function createComponentEditForm() {
    $form = new Form;

    $form->addTextArea('message', 'Text')
            ->addRule(Form::FILLED, 'Text muís byť vyplnený.')
            ->addRule(Form::MAX_LENGTH, 'Maximálna dĺžka textu je 250 znakov.', 250);

    $form->addText('link', 'Odkaz');

    $form->addSubmit('save', 'Zapísať')
            ->onClick[] = [$this, 'submittedEditForm'];

    $form->addSubmit('cancel', 'Zrušiť')
            ->setHtmlAttribute('class', 'btn btn-warning')
            ->onClick[] = [$this, 'formCancelled'];

    FormHelper::setBootstrapRenderer($form);
    return $form;
    }

  /**
   * @param SubmitButton $btn
   * @throws \Nette\Application\AbortException
   */
  public function submittedEditForm(SubmitButton $btn) {
    $this->userIsLogged();
    $values = $btn->form->getValues();
    $this->slideRow->update($values);
    $this->redirect('Homepage:#primary');
  }

  /**
   * @throws \Nette\Application\AbortException
   */
  public function formCancelled() {
    $this->redirect('Homepage:#primary');
  }

}
