<?php

namespace App\Presenters;

use App\Model\PostRepository;
use Nette\Application\UI\Presenter;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Presenter
{
    /** @var PostRepository @inject */
    public $postRepository;
}
