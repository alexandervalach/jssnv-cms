<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\BreadcrumbControl;
use App\Forms\SlideFormFactory;
use App\Model\AlbumsRepository;
use App\Model\SectionsRepository;
use App\Model\SlidesRepository;
use Nette\Application\AbortException;
use Nette\Database\Table\ActiveRow;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class SlidesPresenter
 * @package App\Presenters
 */
class SlidesPresenter extends BasePresenter
{
  /** @var ActiveRow */
  private $slideRow;

  /**
   * @var SlidesRepository
   */
  private $slidesRepository;

  /**
    * @var SlideFormFactory
    */
  private $slideFormFactory;

  /**
   * SlidesPresenter constructor.
   * @param AlbumsRepository $albumsRepository
   * @param SectionsRepository $sectionRepository
   * @param SlidesRepository $slidesRepository
   * @param SlideFormFactory $slideFormFactory
   * @param BreadcrumbControl $breadcrumbControl
   */
  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              SlidesRepository $slidesRepository,
                              SlideFormFactory $slideFormFactory,
                              BreadcrumbControl $breadcrumbControl)
  {
    parent::__construct($albumsRepository, $sectionRepository, $breadcrumbControl);
    $this->slidesRepository = $slidesRepository;
    $this->slideFormFactory = $slideFormFactory;
  }

  /**
   * Prepares data for render template
   */
  public function renderAll(): void
  {
    $this->template->slides = $this->slidesRepository->findAll();
    $this['breadcrumb']->add('Kurzy', 'Slides:all');
  }

  /**
   * @return Form
   */
  protected function createComponentSlideForm(): Form
  {
    return $this->slideFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->userIsLogged();
      $this->getParameter('id' ) ? $this->submittedAddForm($values) : $this->submittedEditForm($values);
    });
  }

  /**
   * Insert new slide to database
   * @param ArrayHash $values
   * @throws AbortException
   */
  public function submittedAddForm(ArrayHash $values): void
  {
    $slide = $this->slidesRepository->insert($values);
    $this->redirect('view', $slide->id);
  }

  /**
   * Updates item with new values
   * @param ArrayHash $values
   * @throws AbortException
   */
  public function submittedEditForm(ArrayHash $values): void
  {
    $this->slideRow->update($values);
    $this->redirect('all');
  }

}
