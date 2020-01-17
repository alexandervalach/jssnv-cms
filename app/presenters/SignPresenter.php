<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\AlbumsRepository;
use App\Model\SectionsRepository;
use App\Forms\SignFormFactory;
use Nette;
use Nette\Security\AuthenticationException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Sign in/out Presenters.
 */
class SignPresenter extends BasePresenter
{
  /**
   * @var SignFormFactory
   */
  private $signFormFactory;

  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              SignFormFactory $signFormFactory)
  {
    parent::__construct($albumsRepository, $sectionRepository);
    $this->signFormFactory = $signFormFactory;
  }

  /**
   * Sign-in form factory.
   * @return Form
   */
  protected function createComponentSignInForm() {
    return $this->signFormFactory->create(function (Form $form, ArrayHash $values) {
      $values->remember ? $this->user->setExpiration('14 days') : $this->user->setExpiration('30 minutes');
      try {
        $this->user->login($values->username, $values->password);
        $this->redirect('Homepage:');
      } catch (AuthenticationException $e) {
        $form->addError($e->getMessage());
        $this->redirect('Homepage:');
      }
    });
  }

  /**
   * @throws Nette\Application\AbortException
   */
  public function actionIn() {
    if ($this->user->isLoggedIn()) {
      $this->redirect('Homepage:');
    }
  }

  /**
   * @throws Nette\Application\AbortException
   */
  public function actionOut() {
    $this->getUser()->logout();
    $this->flashMessage('Boli ste odhlásený.');
    $this->redirect('Homepage:');
  }

}