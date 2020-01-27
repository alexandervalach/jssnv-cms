<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Database\Table\Selection;

/**
 * Class ImagesRepository
 * @package App\Model
 */
class ImagesRepository extends Repository
{
  const ALBUM_ID = 'album_id';

  /**
   * @param int $albumId
   * @return Selection
   */
  public function findForAlbum (int $albumId)
  {
    return $this->findByValue(self::ALBUM_ID, $albumId);
  }
}
