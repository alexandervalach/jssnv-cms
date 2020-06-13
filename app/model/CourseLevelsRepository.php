<?php


namespace App\Model;


class CourseLevelsRepository extends Repository
{
  protected $tableName = 'course_levels';

  public function getAll ()
  {
    return $this->findAll()->fetchPairs(self::ID, self::LABEL);
  }

  public function findAll ()
  {
    return parent::findAll()->order(self::LABEL);
  }
}