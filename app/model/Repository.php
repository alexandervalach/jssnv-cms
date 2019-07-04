<?php

namespace App\Model;

use Nette;

/**
 * Provádí operace nad databázovou tabulkou.
 */
abstract class Repository {

  const IS_PRESENT = 'is_present';

  /** @var Nette\Database\Context */
  private $database;

  /** @var string */
  protected $tableName;

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

  public function getConnection() {
    return $this->database;
  }

  /**
   * Vrací všechny řádky z tabulky.
   * @return Nette\Database\Table\Selection
   */
  public function findAll() {
    return $this->getTable();
  }

  /**
   * Returns only rows that are present in table
   * @return Nette\Database\Table\Selection
   */
  public function getAll() {
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
   * @param type $id identifikátor / primárny kľúč
   * @return Nette\Database\Table\ActiveRow
   */
  public function findById($id) {
    return $this->getTable()->get($id);
  }

  public function update($id, $data) {
    $this->getTable()->wherePrimary($id)->update($data);
  }

  public function insert($data) {
    return $this->getTable()->insert($data);
  }

  public function remove($id) {
    $this->findById($id)->update( array(self::IS_PRESENT => 0) );
  }

}
