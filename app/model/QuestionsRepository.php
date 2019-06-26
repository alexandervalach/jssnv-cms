<?php

namespace App\Model;

use Nette\Database\Table\Selection;

class QuestionsRepository extends Repository {

	/**
	 * @param int $testId
	 * @return Nette\Database\Table\Selection
	 */
    public function findQuestions($testId) {
		return $this->getTable()->where('test_id', $testId)->order('level_id');
    }

}
