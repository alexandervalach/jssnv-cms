<?php

namespace App\Presenters;

use Nette;
use App\Forms\SignFormFactory;

/**
 * Sign in/out presenters.
 */
class SignPresenter extends BasePresenter {

    /** @var SignFormFactory @inject */
    public $factory;

    /**
     * Sign-in form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentSignInForm() {
        $form = $this->factory->create();
        $form->onSuccess[] = function ($form) {
            $form->getPresenter()->redirect('Homepage:#primary');
        };
        return $form;
    }

  /**
   * @throws Nette\Application\AbortException
   */
  public function actionIn() {
        if ($this->user->isLoggedIn()) {
            $this->redirect('Homepage:#primary');
        }
    }

  /**
   * @throws Nette\Application\AbortException
   */
  public function actionOut() {
        $this->getUser()->logout();
        $this->flashMessage('Boli ste odhlásený.');
        $this->redirect('Homepage:#primary');
    }

}