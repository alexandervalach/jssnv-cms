<?php

declare(strict_types=1);

namespace App\Model;

use Nette;

/**
 * Operations on database tables
 */
abstract class Repository {

  const IS_PRESENT = 'is_present';
  const SECTION_ID = 'section_id';
  const TEST_ID = 'test_id';
  const LEVEL_ID = 'level_id';
  const ID = 'id';
  const NAME = 'name';
  const USERNAME = 'username';
  const PRIORITY = 'order';
  const TEXT_COLUMN = 'text';

  /** @var Nette\Database\Context */
  private $database;

  /** @var string */
  protected $tableName;

  /**
   * Repository constructor.
   * @param Nette\Database\Context $database
   */
  public function __construct(Nette\Database\Context $database)
  {
    $this->database = $database;
  }

  /**
   * Returns object representing database table
   * @return Nette\Database\Table\Selection
   */
  protected function getTable()
  {
    if (isset($this->tableName)) {
      return $this->database->table($this->tableName);
    } else {
      // name of table derived from class name
      preg_match('#(\w+)Repository$#', get_class($this), $m);
      return $this->database->table(strtolower($m[1]));
    }
  }

  /**
   * @return Nette\Database\Context
   */
  public function getConnection(): Nette\Database\Context
  {
    return $this->database;
  }

  /**
   * Return rows from table
   * @return Nette\Database\Table\Selection
   */
  public function findAll()
  {
    return $this->getTable()->where(self::IS_PRESENT, 1);
  }

  /**
   * Returns row using filter, array('name' => 'John').
   * @param array $by
   * @return Nette\Database\Table\Selection
   */
  public function findBy(array $by)
  {
    return $this->getTable()->where($by);
  }

  /**
   * Returns a selection from given condition
   * @param $columnName
   * @param $value
   * @return Nette\Database\Table\Selection
   */
  public function findByValue($columnName, $value)
  {
    $condition = array($columnName => $value);
    return $this->findBy($condition);
  }

  /**
   * Returns row from id
   * @param int $id id / primary key
   * @return Nette\Database\Table\ActiveRow|null
   */
  public function findById(int $id)
  {
    $item = $this->getTable()->get($id);
    return $item->is_present ? $item : null;
  }

  /**
   * Updates data for selected item
   * @param $id
   * @param $data
   */
  public function update($id, $data): void
  {
    $this->getTable()->wherePrimary($id)->update($data);
  }

  /**
   * @param $data
   * @return bool|int|Nette\Database\Table\ActiveRow
   */
  public function insert($data)
  {
    return $this->getTable()->insert($data);
  }

  /**
   * Softly deletes row from table
   * @param int $id
   */
  public function softDelete(int $id)
  {
    $this->findById($id)->update( array(self::IS_PRESENT => 0) );
  }

}
