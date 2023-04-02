<?php

namespace Viktoriya\PHP2\Http\Auth;

use Viktoriya\PHP2\Blog\Exceptions\AuthException;
use Viktoriya\PHP2\Blog\Exceptions\HttpException;
use Viktoriya\PHP2\Blog\Exceptions\UserNotFoundException;
use Viktoriya\PHP2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Viktoriya\PHP2\Blog\User;
use Viktoriya\PHP2\Http\Request;

class PasswordAuthentication implements PasswordAuthenticationInterface
{
  public function __construct(
    private UsersRepositoryInterface $usersRepository
  ) {
  }

  /**
   * @throws AuthException
   */
  public function user(Request $request): User
  {
    try {
      $username = $request->jsonBodyField('username');
    } catch (HttpException $e) {
      throw new AuthException($e->getMessage());
    }
    try {
      $user = $this->usersRepository->getByUsername($username);
    } catch (UserNotFoundException $e) {
      throw new AuthException($e->getMessage());
    }
    try {
      $password = $request->jsonBodyField('password');
    } catch (HttpException $e) {
      throw new AuthException($e->getMessage());
    }

    if (!$user->checkPassword($password)) {
      throw new AuthException('Wrong password');
    }
    return $user;
  }
}
