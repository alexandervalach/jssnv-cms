<?php

declare(strict_types=1);

namespace App;

use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;
use Nette\Application\IRouter;

/**
 * Router factory.
 */
class RouterFactory
{
  /**
   * Generates all routes
   * @return IRouter
   */
  public static function createRouter()
  {
    $router = new RouteList();
    $router[] = new Route('admin', 'Sign:in');
    $router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
    $router[] = new Route('<presenter>/<action>/<expression>', 'Search:all');
    return $router;
  }
}
