<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Database\Table\Selection;

/**
 * Class ResultsRepository
 * @package App\Model
 */
class ResultsRepository extends Repository {

  /**
   * @return Selection
   */
  public function findAllAndOrder ()
  {
    return $this->findAll()->order(self::ID . ' DESC');
  }
}
