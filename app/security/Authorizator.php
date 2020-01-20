<?php

declare(strict_types=1);

namespace App\Security;

use Nette\Security\IAuthorizator;

class Authorizator implements IAuthorizator
{
  private $roles = ['guest', 'admin', 'root'];

  public function __construct()
  {

  }

  /**
   * @inheritDoc
   */
  function isAllowed($role, $resource, $privilege): bool
  {
    /*
    foreach ($this->getRoles() as $role) {
      if ($this->getAuthorizator()->isAllowed($role, $resource, $privilege)) {
        return true;
      }
    }
    */
    return true;
  }
}