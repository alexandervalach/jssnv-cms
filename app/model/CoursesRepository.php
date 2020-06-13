<?php

declare(strict_types=1);

namespace App\Model;

class CoursesRepository extends Repository
{
  public function getAll ()
  {
    return $this->findAll()->fetchPairs(self::ID, self::LABEL);
  }

  public function findAll()
  {
    return parent::findAll()->order(self::LABEL);
  }
}