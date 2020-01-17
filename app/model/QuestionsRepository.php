<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Database\Table\Selection;

/**
 * Class QuestionsRepository
 * @package App\Model
 */
class QuestionsRepository extends Repository
{
  /**
   * @param int $testId
   * @return Selection
  */
  public function findQuestions(int $testId)
  {
    return $this->getTable()->where(self::TEST_ID, $testId)->order(self::LEVEL_ID);
  }
}
