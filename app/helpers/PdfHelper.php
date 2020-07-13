<?php

declare(strict_types=1);

namespace App\Helpers;

use Nette\Application\UI\ITemplate;
use Nette\Database\Table\ActiveRow;

class PdfHelper
{
  public static function fillApplicationTemplateWithData (ITemplate $template, ActiveRow $applicationForm) {
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
    $template->branch_name = $branch->hq === 0 ? $branch->label : null;
    $template->fname = $fullname['fname'];
    $template->lname = $fullname['lname'];
    $template->title = ApplicationHelper::parseTitles($applicationForm->title_bn, $applicationForm->title_an);
    $template->address = ApplicationHelper::parseAddress($applicationForm->street_address, $applicationForm->city);
    $template->zipcode = $applicationForm->zipcode;
    $template->birthdate = $applicationForm->birthdate;
    $template->birthplace = $applicationForm->birthplace;
    $template->id_number = $applicationForm->id_number;
    $template->nationality = $applicationForm->nationality;
    $template->email = $applicationForm->email;
    $template->phone = $applicationForm->phone;
    $template->employment = $applicationForm->employment;
    $template->prev_course = $applicationForm->prev_course ? $applicationForm->prev_course : '-';
    $template->prev_school_year = ApplicationHelper::getPrevSchoolYear();
    $template->sign_date = ApplicationHelper::parseSignDate((string) $applicationForm->updated_at);
  }

  public static function fillDecisionTemplateWithData (ITemplate $template, ActiveRow $applicationForm) {
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
      $template->language = $course->acronym;
      $template->class = $courseLevel->label;
    }

    $template->decision_id = '12345678';
    $template->year_id = ApplicationHelper::getSchoolYear('-');
    $template->today = date('d.m.Y');
    $template->fullname = $applicationForm->name;
    $template->birthdate = $applicationForm->birthdate;
    $template->address = ApplicationHelper::parseAddress($applicationForm->street_address, $applicationForm->city);
    $template->school_year = ApplicationHelper::getSchoolYear();
  }
}