<?php

declare(strict_types=1);

namespace App\Helpers;

use Nette\Application\UI\ITemplate;
use Nette\Database\Table\ActiveRow;

class PdfHelper
{
  public static function fillTemplateWithData (ITemplate $template, ActiveRow $applicationForm) {
    $fullname = ApplicationHelper::parseFullName($applicationForm->name);
    $branchClass = $applicationForm->ref('branches_classes', 'branch_class_id');
    $branch;
    $class;
    $course;
    $courseLevel;

    if ($branchClass) {
      $branch = $branchClass->ref('branches', 'branch_id');
      $class = $branchClass->ref('classes', 'class_id');
    }

    if ($class) {
      $course = $class->ref('courses', 'course_id');
      $courseLevel = $class->ref('course_levels', 'course_level_id');
      $template->course_name = $course->label . ', ' . $courseLevel->label;
    }

    $template->school_year = date('Y') . '/' . date('Y', strtotime('+1 year'));
    $template->branch_name = $branch->label;
    $template->fname = $fullname['fname'];
    $template->lname = $fullname['lname'];
    $template->title = ApplicationHelper::parseTitles($applicationForm->title_bn, $applicationForm->title_an);
    $template->address = $applicationForm->street_address . ', ' . $applicationForm->city;
    $template->zipcode = $applicationForm->zipcode;
    $template->birthdate = $applicationForm->birthdate;
    $template->birthplace = $applicationForm->birthplace;
    $template->id_number = $applicationForm->id_number;
    $template->nationality = $applicationForm->nationality;
    $template->email = $applicationForm->email;
    $template->phone = $applicationForm->phone;
    $template->employment = $applicationForm->employment;
    $template->prev_course = $applicationForm->prev_course ? $applicationForm->prev_course : '-';
    $template->prev_school_year = date('Y', strtotime('-1 year')) . '/' . date('Y');
    $template->today = date('d.m.Y');
  }
}