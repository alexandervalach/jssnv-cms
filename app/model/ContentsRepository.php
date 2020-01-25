<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Database\ResultSet;

/**
 * Class ContentsRepository
 * @package App\Model
 */
class ContentsRepository extends Repository
{
  /**
   * @var array
   */
  public static $type = array(
    'file' => 1,
    'image' => 2,
    'text' => 3,
    'video' => 4
  );

  /**
   * @param string $text
   * @return ResultSet
   */
  public function findByText (string $text)
  {
    $con = $this->getConnection();
    $searchString = '%' . $text . '%';
    return $con->query('SELECT c.id, c.section_id, c.text, c.type, s.section_id AS parent_id, s.name AS section_name, c.updated_at
        FROM contents AS c 
        JOIN sections AS s 
        ON c.section_id = s.id 
        WHERE (s.name LIKE ? OR c.text LIKE ?) 
        AND c.is_present = 1 AND s.is_present = 1
        ORDER BY c.updated_at', $searchString, $searchString);
  }
}
