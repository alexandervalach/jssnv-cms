<?php

namespace App\Model;

class NoticesRepository extends Repository {

  public static $flag = array(
    'success' => 'zelený',
    'info' => 'modrý',
    'warning' => 'žltý',
    'danger' => 'červený'
  );

  public function getAll() {
    return $this->findAll()->where(self::IS_PRESENT, 1)->order('updated_at DESC');
  }

}
