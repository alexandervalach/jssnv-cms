<?php

namespace App\Components;

use Nette\Application\UI\Control;

class FormCancelled extends Control {

    public function render($link, $text, $class, $icon = null) {
        $this->template->setFile(__DIR__ . '/FormCancelled.latte');
        $this->template->link = $link;
        $this->template->text = $text;
        $this->template->class = $class;
        $this->template->icon = $icon;
        $this->template->render();
    }

}
