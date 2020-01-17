<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Database\IRow;
use Nette\Database\Table\ActiveRow;

/**
 * Class UsersRepository
 * @package App\Model
 */
class UsersRepository extends Repository
{
  /**
   * @param string $username
   * @return IRow|ActiveRow|null
   */
  public function fetchByName (string $username) {
    return $this->findAll()->where(self::USERNAME, $username)->fetch();
  }
}
