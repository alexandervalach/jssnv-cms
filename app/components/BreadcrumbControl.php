<?php

declare(strict_types=1);

namespace App\Components;

use Nette\Application\UI\Control;
use Nette\Application\UI\InvalidLinkException;

/**
 * Class BreadcrumbControl
 */
class BreadcrumbControl extends Control
{
  /** @var array */
  private $homepage = [];

  /** @var array */
  private $items = [];

  /** @var string */
  private $customTemplate = [];

  /**
   * @param string $path
   */
  public function setTemplate(string $path): void
  {
    $this->customTemplate = $path;
  }

  /**
   * @param string $name
   * @param string $link
   * @return $this
   */
  public function setHomepage($name = '', $link = '')
  {
    $this->homepage = [
      'name' => $name,
      'link' => $link
    ];
    return $this;
  }

  /**
   * @param string $name
   * @param string $link
   * @return $this
   */
  public function add($name = '', $link = '')
  {
    $this->items[] = [
      'name' => $name,
      'link' => $link
    ];
    return $this;
  }

  /**
   * @throws InvalidLinkException
   */
  public function render(): void
  {
    if ($this->customTemplate == FALSE) {
      $this->customTemplate = __DIR__ . '/BreadcrumbControl.latte';
    }

    $this->template->homepage = !empty($this->homepage) ? $this->homepage : [
      'name' => 'Domov',
      'link' => $this->presenter->link('Homepage:')
    ];

    $this->template->items = $this->items;
    $this->template->backLink = (count($this->items) >= 2 and !empty($this->items[count($this->items)-2]['link'])) ? $this->items[count($this->items)-2] : FALSE;
    $this->template->setFile($this->customTemplate);
    $this->template->render();
  }

}
