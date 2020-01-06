<?php

namespace App\Model;

/**
 * Class UsersRepository
 * @package App\Model
 */
class UsersRepository extends Repository {

  public function fetchByName (string $username) {
    return $this->findAll()->where(self::USERNAME, $username)->fetch();
  }
}
