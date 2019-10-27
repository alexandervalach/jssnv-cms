<?php

declare(strict_types=1);

namespace App\Model;

use Nette;

/**
 * Provádí operace nad databázovou tabulkou.
 */
abstract class Repository {

  const IS_PRESENT = 'is_present';
  const SECTION_ID = 'section_id';
  const VISIBLE = 'visible';
  const ID = 'id';
  const NAME = 'name';
  const USERNAME = 'username';

  /** @var Nette\Database\Context */
  private $database;

  /** @var string */
  protected $tableName;

  /**
   * Repository constructor.
   * @param Nette\Database\Context $database
   */
  public function __construct(Nette\Database\Context $database) {
    $this->database = $database;
  }

  /**
   * Vrací objekt reprezentující databázovou tabulku.
   * @return Nette\Database\Table\Selection
   */
  protected function getTable() {
    if (isset($this->tableName)) {
      return $this->database->table($this->tableName);
    } else {
      // název tabulky odvodíme z názvu třídy
      preg_match('#(\w+)Repository$#', get_class($this), $m);
      return $this->database->table(strtolower($m[1]));
    }
  }

  /**
   * @return Nette\Database\Context
   */
  public function getConnection() {
    return $this->database;
  }

  /**
   * Vrací všechny řádky z tabulky.
   * @return Nette\Database\Table\Selection
   */
  public function findAll() {
    return $this->getTable()->where(self::IS_PRESENT, 1);
  }

  /**
   * Vrací řádky podle filtru, např. array('name' => 'John').
   * @return Nette\Database\Table\Selection
   */
  public function findBy(array $by) {
    return $this->getTable()->where($by);
  }

  /**
   * Vracia selection podľa jednej podmienky.
   * @param type $columnName
   * @param type $value
   * @return Nette\Database\Table\Selection
   */
  public function findByValue($columnName, $value) {
    $condition = array($columnName => $value);
    return $this->findBy($condition);
  }

  /**
   * Vráti riadok podľa ID.
   * @param int $id identifikátor / primárny kľúč
   * @return Nette\Database\Table\ActiveRow
   */
  public function findById(int $id) {
    $item = $this->getTable()->get($id);
    return $item->is_present ? $item : null;
  }

  /**
   * @param $id
   * @param $data
   */
  public function update($id, $data) {
    $this->getTable()->wherePrimary($id)->update($data);
  }

  /**
   * @param $data
   * @return bool|int|Nette\Database\Table\ActiveRow
   */
  public function insert($data) {
    return $this->getTable()->insert($data);
  }

  /**
   * @param int $id
   */
  public function softDelete(int $id) {
    $this->findById($id)->update( array(self::IS_PRESENT => 0) );
  }

}
