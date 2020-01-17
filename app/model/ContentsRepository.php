<?php

declare(strict_types=1);

namespace App\Model;

/**
 * Class ContentsRepository
 * @package App\Model
 */
class ContentsRepository extends Repository{
  /**
   * @var array
   */
  public static $type = array(
    'file' => 1,
    'image' => 2,
    'text' => 3,
    'video' => 4
  );
}
