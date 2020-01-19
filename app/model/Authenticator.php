<?php

declare(strict_types=1);

namespace App\Model;

use Nette;
use Nette\Security\Passwords;
use Nette\Security\IIdentity;

/**
 * Basic authenticator
 */
class Authenticator implements Nette\Security\IAuthenticator {

  /** @var UsersRepository */
  private $usersRepository;

  /** @var Passwords */
  private $passwords;

  /**
   * Authenticator constructor.
   * @param UsersRepository $usersRepository
   * @param Passwords $passwords
   */
  public function __construct(UsersRepository $usersRepository, Passwords $passwords) {
    $this->usersRepository = $usersRepository;
    $this->passwords = $passwords;
  }

  /**
   * Performs an authentication.
   * @param array $credentials
   * @return Nette\Security\IIdentity
   * @throws Nette\Security\AuthenticationException
   */
    public function authenticate(array $credentials): IIdentity
    {
      [$username, $password] = $credentials;

      $row = $this->usersRepository->fetchByName($username);

      if (!$row) {
        throw new Nette\Security\AuthenticationException('Používateľ neexistuje.', self::IDENTITY_NOT_FOUND);
      }

      if (!$this->passwords->verify($password, (string)$row->password)) {
        throw new Nette\Security\AuthenticationException('Nesprávne heslo.', self::INVALID_CREDENTIAL);
      }

      return new Nette\Security\Identity($row->id, $row->role, ['username' => $row->username]);
    }

}
