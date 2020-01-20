<?php

declare(strict_types=1);

namespace App\Security;

use Nette\Security\Permission;
use Nette\SmartObject;

class PermissionFactory
{
  use SmartObject;

  public static function create(): Permission
  {
    $acl = new Permission();

    // Add roles
    $acl->addRole('root');
    $acl->addRole('admin');
    $acl->addRole('guest');

    // Add resources
    $acl->addResource('Albums');
    $acl->addResource('Answers');
    $acl->addResource('Contact');
    $acl->addResource('Files');
    $acl->addResource('Homepage');
    $acl->addResource('Images');
    $acl->addResource('Notices');
    $acl->addResource('Questions');
    $acl->addResource('Results');
    $acl->addResource('Sections');
    $acl->addResource('Sign:in');
    $acl->addResource('Sign:out');
    $acl->addResource('Slides');
    $acl->addResource('Tests');
    $acl->addResource('Users');

    $acl->allow('guest', 'Albums', 'all');
    $acl->allow('guest', 'Albums', 'view');
    $acl->allow('guest', 'Tests', 'run');
    $acl->deny('guest', 'Tests', 'all');
    $acl->deny('guest', 'Tests', 'view');

    return $acl;
  }

  private function allow(Permission $permission, string $role, array $action)
  {
    list($resource, $privilege) = $action;
    $permission->allow($role, $resource, $privilege);
  }
}