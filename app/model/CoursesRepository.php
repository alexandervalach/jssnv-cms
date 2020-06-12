<?php

declare(strict_types=1);

namespace App\Model;

class CoursesRepository extends Repository
{
  public function getAll ()
  {
    return $this->findAll()->order(self::LABEL)->fetchPairs(self::ID, self::LABEL);
  }
}