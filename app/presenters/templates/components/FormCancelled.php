<?php

namespace App\Components;

use Nette\Application\UI\Control;

class FormCancelled extends Control {

    public function render($link, $text, $icon = null) {
        $this->template->setFile(__DIR__ . '/FormCancelled.latte');
        $this->template->link = $link;
        $this->template->icon = $icon;
        $this->template->text = $text;
        $this->template->render();
    }

}
