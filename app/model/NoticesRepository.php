<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Database\Table\Selection;

/**
 * Class NoticesRepository
 * @package App\Model
 */
class NoticesRepository extends Repository {

  /**
   * @var array
   */
  public static $flag = array(
    'success' => 'zelený',
    'info' => 'modrý',
    'warning' => 'žltý',
    'danger' => 'červený'
  );

  /**
   * @return Selection
   */
  public function getAll() {
    return $this->findAll()->where(self::IS_PRESENT, 1)->order('updated_at DESC');
  }

  public function findAllAndOrder ()
  {
    return $this->findAll()->order('updated_at DESC');
  }

}
