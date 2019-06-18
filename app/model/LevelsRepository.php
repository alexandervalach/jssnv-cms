<?php

namespace App\Model;

class LevelsRepository extends Repository {
    
    public function getLevels() {
      $levels = $this->findAll()->order('label ASC');

      if (!$levels) {
        return null;
      }

      $list = [];
      foreach ($levels as $level) {
        $list[$level->id] = $level->label;
      }
      return $list;
    }
}
