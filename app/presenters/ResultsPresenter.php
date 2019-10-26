<?php

namespace App\Presenters;

use App\FormHelper;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;

namespace App\Presenters;

/**
 * Class ResultsPresenter
 * @package App\Presenters
 */
class ResultsPresenter extends BasePresenter {

    /** @var array */
    private $levelsResults;

    /** @var ActiveRow */
    private $resultRow;

  /**
   * @throws \Nette\Application\AbortException
   */
  public function actionAll () {
    	if (!$this->user->isLoggedIn()) {
    		$this->redirect('Homepage:');
    	}
    }

  /**
   *
   */
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

  /**
   * @param $id
   * @throws \Nette\Application\BadRequestException
   */
  public function actionView ($id) {
      $this->resultRow = $this->resultsRepository->findById($id);
      $this->levelsResults = array();

      if (!$this->resultRow) {
        $this->error(self::ITEM_NOT_FOUND);
      }

      $levelsResults = $this->levelsResultsRepository->findAll()->where('result_id', $this->resultRow->id);

      foreach ($levelsResults as $result) {
        $this->levelsResults[$result->level_id]['score'] = $result->score;
        $this->levelsResults[$result->level_id]['label'] = $result->ref('levels', 'level_id')->label;
      }
    }

  /**
   * @param $id
   */
  public function renderView ($id) {
      $this->template->result = $this->resultRow;
      $this->template->levels = $this->levelsResults;
      $this->template->test = $this->resultRow->ref('tests', 'test_id');
    }
}
