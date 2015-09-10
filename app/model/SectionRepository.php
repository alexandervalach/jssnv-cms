<?php

namespace App\Model;

class SectionRepository extends Repository {

    public function getSections() {
        $sections = $this->findAll()->where('link = ?', ' ');

        if (!$sections) {
            return null;
        }

        foreach ($sections as $section) {
            $list[$section->id] = $section->name;
        }
        return $list;
    }

}
