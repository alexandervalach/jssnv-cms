<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Database\ResultSet;

class ApplicationFormsRepository extends Repository
{
  protected $tableName = 'application_forms';

  /**
   * @return ResultSet
   */
  public function getAll (): ResultSet
  {
    $db = $this->getConnection();
    return $db->query('SELECT af.id, af.name, af.title_bn, af.title_an, af.status, af.email, af.phone, af.consent_name, af.consent_photo, 
      cr.id AS course_id, cr.label AS course_label, b.id AS branch_id, b.label AS branch_label,
      cl.id AS course_level_id, cl.label AS course_level_label
      FROM application_forms AS af
      JOIN branches_classes AS bc ON af.branch_class_id = bc.id
      JOIN branches AS b ON bc.branch_id = b.id
      JOIN classes AS c ON bc.branch_id = c.id
      JOIN courses AS cr ON c.course_id = cr.id
      JOIN course_levels AS cl ON c.course_level_id = cl.id 
      WHERE af.is_present = 1 AND af.status != \'archived\'
      ORDER BY id DESC');
  }

  /**
   * @return array
   */
  public function fetchAll (): array
  {
    return $this->getAll()->fetchAll();
  }

  public function get (int $id): ResultSet
  {
    $db = $this->getConnection();
    return $db->query('SELECT af.id, af.name, af.title_bn, af.title_an, af.status, af.email, af.phone, 
      af.consent_name, af.consent_photo,  af.street_address, af.city, af.zipcode, af.birthdate, af.birthplace, 
      af.id_number, af.nationality, af.employment, af.prev_course, af.consent_personal_data, af.note,
      cr.id AS course_id, cr.label AS course_label, b.id AS branch_id, b.label AS branch_label,
      cl.id AS course_level_id, cl.label AS course_level_label
      FROM application_forms AS af
      JOIN branches_classes AS bc ON af.branch_class_id = bc.id
      JOIN branches AS b ON bc.branch_id = b.id
      JOIN classes AS c ON bc.branch_id = c.id
      JOIN courses AS cr ON c.course_id = cr.id
      JOIN course_levels AS cl ON c.course_level_id = cl.id 
      WHERE af.is_present = 1 AND af.status != \'archived\' AND af.id = ?', $id);
  }

  public function fetch (int $id)
  {
    return $this->get($id)->fetch();
  }
}