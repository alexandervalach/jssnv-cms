<?php

declare(strict_types=1);

namespace App\Model;

/**
 * Class TestsRepository
 * @package App\Model
 */
class TestsRepository extends Repository {

  public function findAll()
  {
    return parent::findAll()->order(self::LABEL);
  }

}
