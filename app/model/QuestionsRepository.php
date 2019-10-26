<?php

namespace App\Model;

use Nette\Database\Table\Selection;

/**
 * Class QuestionsRepository
 * @package App\Model
 */
class QuestionsRepository extends Repository {

	/**
	 * @param int $testId
	 * @return Selection
	 */
    public function findQuestions($testId) {
		return $this->getTable()->where('test_id', $testId)->order('level_id');
    }

}
