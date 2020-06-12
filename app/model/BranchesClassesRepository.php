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
}