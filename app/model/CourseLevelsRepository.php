<?php


namespace App\Model;


class CourseLevelsRepository extends Repository
{
  protected $tableName = 'course_levels';

  public function getAll ()
  {
    return $this->findAll()->order(self::LABEL)->fetchPairs(self::ID, self::LABEL);
  }
}