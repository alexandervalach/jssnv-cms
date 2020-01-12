<?php

declare(strict_types=1);

namespace App\Presenters;

use App\FormHelper;
use App\Forms\SlideFormFactory;
use App\Model\AlbumsRepository;
use App\Model\SectionsRepository;
use App\Model\SlidesRepository;
use Nette\Application\AbortException;
use Nette\Database\Table\ActiveRow;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;

/**
 * Class SlidesPresenter
 * @package App\Presenters
 */
class SlidesPresenter extends BasePresenter {

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

  public function __construct(AlbumsRepository $albumsRepository,
                              SectionsRepository $sectionRepository,
                              SlidesRepository $slidesRepository,
                              SlideFormFactory $slideFormFactory)
  {
    parent::__construct($albumsRepository, $sectionRepository);
    $this->slidesRepository = $slidesRepository;
    $this->slideFormFactory = $slideFormFactory;
  }

  /**
   * Prepares data for render template
   */
  public function renderAll(): void
  {
    $this->template->slides = $this->slidesRepository->findAll();
  }

  /**
   * @return Form
   */
  protected function createComponentSlideForm(): Form
  {
    return $this->slideFormFactory->create(function (Form $form, ArrayHash $values) {
      $this->userIsLogged();
      $this->getParameter('id' ) ? $this->submittedAddSlideForm($values) : $this->submittedEditSlideForm($values);
    });
  }

  /**
   * Insert new slide to database
   * @param ArrayHash $values
   * @throws AbortException
   */
  public function submittedAddSlideForm(ArrayHash $values) {
    $slide = $this->slidesRepository->insert($values);
    $this->redirect('view', $slide->id);
  }

  /**
   * Updates item with new values
   * @param ArrayHash $values
   * @throws AbortException
   */
  public function submittedEditSlideForm(ArrayHash $values) {
    $this->slideRow->update($values);
    $this->redirect('all');
  }

}
