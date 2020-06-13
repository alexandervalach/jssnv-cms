<?php

declare(strict_types=1);

namespace App\Model;

class BranchesRepository extends Repository
{
  public function findAll ()
  {
    return parent::findAll()->order(self::LABEL);
  }
}