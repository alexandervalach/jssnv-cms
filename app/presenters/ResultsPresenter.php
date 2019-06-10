<?php

namespace App\Presenters;

use App\FormHelper;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;

namespace App\Presenters;

class ResultsPresenter extends BasePresenter {

    /** @var ActiveRow */
    private $resultRow;

    public function actionAll () {
    	if (!$this->user->isLoggedIn()) {
    		$this->redirect('Homepage:');
    	}
    }

    public function renderAll () {
    	$results = $this->resultsRepository->findAll();
    	$data = [];

    	foreach ($results as $result) {
    		$data[] = array(
    			'result' => $result,
    			'test' => $result->ref('tests', 'test_id')
    		);
    	}

    	$this->template->results = $data;
    }

    public function actionView ($id) {
      $this->resultRow = $this->resultsRepository->findById($id);
      
      if (!$this->resultRow) {
        $this->error(self::ITEM_NOT_FOUND);
      }
    }

    public function renderView ($id) {
      $this->template->result = $this->resultRow;
      $this->template->test = $this->resultRow->ref('tests', 'test_id');
    }
}
