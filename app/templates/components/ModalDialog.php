<?php

namespace App\Components;

use Nette\Application\UI\Control;

class ModalDialog extends Control {

    public function render($action, $title, $question, $style, $buttonText, $icon, $objectId = NULL) {
        $this->template->setFile(__DIR__ . '/ModalDialog.latte');
        $this->template->action = $action;
        $this->template->title = $title;
        $this->template->question = $question;
        $this->template->style = $style;
        $this->template->buttonText = $buttonText;
        $this->template->icon = $icon;
        $this->template->objectId = $objectId;
        $this->template->render();
    }

}
