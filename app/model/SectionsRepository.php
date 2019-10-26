<?php

namespace App\Model;

/**
 * Class SectionRepository
 * @package App\Model
 */
class SectionsRepository extends Repository {

  public function findByParent($parentId) {
    return $this->findAll()->where(self::SECTION_ID, $parentId)
        ->where(self::VISIBLE, 1);
  }

  /**
   * @return array|null
   */
  public function getSections() {
    $sections = $this->findAll()->order('name ASC');

    if (!$sections) {
      return null;
    }

    $list = [];

    foreach ($sections as $section) {
      $list[$section->id] = $section->name;
    }

    return $list;
  }

  /**
   * @return array
   */
  public function fetchAll () {
    return array('0' => 'Žiadna') + $this->findAll()->where(self::SECTION_ID, null)->fetchPairs(self::ID, self::NAME);
  }

}
