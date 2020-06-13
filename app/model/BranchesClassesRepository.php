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
    $branchClasses = $this->findByValue('branch_id', $branchId);
    $res = [];

    foreach ($branchClasses as $branchClass) {
      $class = $branchClass->ref('classes', 'class_id');
      $course = $class->ref('courses', 'course_id');
      $courseLevel = $class->ref('course_levels', 'course_level_id');

      if (empty($res[$course->label])) {
        $res[$course->label] = [];
      }

      $res[$course->label][$branchClass->id] = $courseLevel->label;
    }

    return $res;
  }
}