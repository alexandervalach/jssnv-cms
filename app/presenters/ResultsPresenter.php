<?php

namespace App\Presenters;

use App\FormHelper;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;

namespace App\Presenters;

class ResultsPresenter extends BasePresenter {

    /** @var array */
    private $levelsResults;

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
          'data' => $result,
    			'levels' => $result->related('levels_results'),
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

      $levelsResults = $this->resultRow->related('levels_results');

      foreach ($levelsResults as $result) {
        $this->levelsResults['score'] = $result->score;
        $level = $result->ref('levels', 'level_id');
        $this->levelsResults['level'] = $level->label;
      }
    }

    public function renderView ($id) {
      $this->template->result = $this->resultRow;
      $this->template->levels = $this->levelsResults;
      $this->template->test = $this->resultRow->ref('tests', 'test_id');
    }
}
