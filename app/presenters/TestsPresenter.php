<?php

namespace App\Presenters;

use App\FormHelper;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;

namespace App\Presenters;

class TestsPresenter extends BasePresenter {

    /** @var ActiveRow */
    private $testRow;

    public function actionAll () {
    	if (!$this->user->isLoggedIn()) {
    		$this->redirect('Homepage:');
    	}
    }

    public function renderAll () {
    	$this->template->tests = $this->testsRepository->findAll();
    }

    public function actionView ($id) {
        $this->testRow = $this->testsRepository->findById($id);
    }

    public function renderView ($id) {
        if (!$this->testRow) {
            throw new BadRequestException(self::TEST_NOT_FOUND);
        }

        $this->template->test = $this->testRow;
        // $this->testRow->related('questions');
    }

    public function actionEdit ($id) {
        $this->userIsLogged();
        $this->testRow = $this->testsRepository->findById($id);
    }

    public function renderEdit ($id) {
        if (!$this->testRow) {
            throw new BadRequestException(self::TEST_NOT_FOUND);
        }
        $this->template->test = $this->testsRepository->findById($id);
    }

}
