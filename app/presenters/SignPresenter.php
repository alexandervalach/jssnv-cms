<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\BreadcrumbControl;
use App\Forms\SearchFormFactory;
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

  /**
   * SignPresenter constructor.
   * @param AlbumsRepository $albumsRepository
   * @param SectionsRepository $sectionRepository
   * @param BreadcrumbControl $breadcrumbControl
   * @param SearchFormFactory $searchFormFactory
   * @param SignFormFactory $signFormFactory
   */
  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              BreadcrumbControl $breadcrumbControl,
                              SearchFormFactory $searchFormFactory,
                              SignFormFactory $signFormFactory)
  {
    parent::__construct($albumsRepository, $sectionRepository, $breadcrumbControl, $searchFormFactory);
    $this->signFormFactory = $signFormFactory;
  }

  /**
   * Sign-in form factory.
   * @return Form
   */
  protected function createComponentSignInForm(): Form
  {
    return $this->signFormFactory->create(function (Form $form, ArrayHash $values) {
      $values->remember ? $this->user->setExpiration('7 days') : $this->user->setExpiration('30 minutes');
      try {
        $this->user->login($values->username, $values->password);
        $this->flashMessage('Vitajte v administrácii JSSNV!', self::SUCCESS);
        $this->redirect('Homepage:');
      } catch (AuthenticationException $e) {
        $form->addError($e->getMessage());
        $this->redirect('Sign:in');
      }
    });
  }

  /**
   * @throws Nette\Application\AbortException
   */
  public function actionIn(): void
  {
    if ($this->user->isLoggedIn()) {
      $this->redirect('Homepage:');
    }
  }

  /**
   * @throws Nette\Application\AbortException
   */
  public function actionOut(): void
  {
    $this->getUser()->logout();
    $this->flashMessage('Boli ste odhlásený.');
    $this->redirect('Homepage:');
  }

}