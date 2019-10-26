<?php

namespace App;

use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;
use Nette\Forms\Controls\SelectBox;
use Nette\Forms\Controls\SubmitButton;
use Nette\Forms\Controls\TextArea;
use Nette\Forms\Controls\TextInput;
use Nette\Utils\Strings;

/**
 * Class FormHelper
 * @package App
 */
class FormHelper {

  /**
   * @param Form $form
   */
  public static function setBootstrapRenderer(Form $form) {
        $renderer = $form->getRenderer();
        $renderer->wrappers['controls']['container'] = NULL;
        $renderer->wrappers['form']['container'] = NULL;
        $renderer->wrappers['pair']['container'] = 'div class="form-group"';
        $renderer->wrappers['label']['container'] = NULL;
        $renderer->wrappers['control']['container'] = NULL;
        $renderer->wrappers['group']['label'] = NULL;
        $renderer->wrappers['group']['container'] = NULL;
        $renderer->wrappers['group']['p'] = NULL;

        foreach ($form->getComponents() as $component) {
            if ($component instanceof TextInput || $component instanceof TextArea) {
                $component->getControlPrototype()->class = "form-control";
            }
            if ($component instanceof SubmitButton) {
                if (empty($component->getControlPrototype()->class)) {
                    $component->getControlPrototype()->class = "btn btn-success";
                }
            }
            if ($component instanceof SelectBox) {
                $component->getControlPrototype()->class = "form-control";
                //$component->setAttribute('data-live-search', 'true');
            }
        }
    }

  /**
   * @param Form $form
   * @param ActiveRow $row
   * @return string
   */
  public static function getEditFormLogNotice(Form $form, ActiveRow $row) {
        $newFormValues = $form->getValues();
        $changedData = array();
        
        foreach ($newFormValues as $key => $newValue) {
            $oldValue = $row[$key];
            $modified = $newValue != $oldValue;
            
            if (empty($oldValue)) {
                $oldValue = "NULL";
            } else if (Strings::endsWith($key, '_id')) {
                $formItems = $form->getComponent($key)->getItems();
                $oldValue = $formItems[$oldValue];
            }
            
            if (empty($newValue)) {
                $newValue = "NULL";
            } else if (Strings::endsWith($key, '_id')) {
                $formItems = $form->getComponent($key)->getItems();
                $newValue = $formItems[$newValue];
            }
            
            if ($modified) {
                $changedData[] = "($key) $oldValue ⇒ $newValue";
            }
        }
        
        return implode("\n", $changedData);
    }

}
