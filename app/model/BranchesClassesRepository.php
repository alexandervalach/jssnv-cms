<?php

declare(strict_types=1);

namespace App\Model;

class BranchesClassesRepository extends Repository
{
  protected $tableName = 'branches_classes';

  public function getForBranch (int $branchId) {
    $items = $this->findByValue('branch_id', $branchId);
    $res = [];

    foreach ($items as $item) {
      $class = $item->ref('classes', 'class_id');
      $course = $class->ref('courses', 'course_id');
      $course_level = $class->ref('course_levels', 'course_level_id');

      $res[] = [
        'class' => $class,
        'course' => $course,
        'course_level' => $course_level
      ];
    }

    return $res;
  }

  public function getForApplicationForm (int $branchId) {
    $db = $this->getConnection();
    return $db->query('SELECT bc.id, cr.label AS course_label, 
      cl.label AS course_level_label, cr.id AS course_id 
      FROM branches_classes AS bc
      JOIN branches AS b ON b.id = bc.branch_id
      JOIN classes AS c ON c.id = bc.class_id
      JOIN courses AS cr ON c.course_id = cr.id
      JOIN course_levels AS cl ON c.course_level_id = cl.id
      WHERE bc.branch_id = ? AND bc.is_present = 1
      ORDER BY cr.label, cl.label', $branchId);
  }

  public function fetchForApplicationForm (int $branchId) {
    $branchClasses = $this->getForApplicationForm($branchId)->fetchAll();
    $res = [];

    foreach ($branchClasses as $class) {
      if (!array_key_exists($class->course_label, $res)) {
        $res[$class->course_label] = [];
      }
      $res[$class->course_label][$class->id] = $class->course_level_label;
    }

    return $res;
  }
}