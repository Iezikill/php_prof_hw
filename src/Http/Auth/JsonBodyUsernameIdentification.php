<?php

namespace Viktoriya\PHP2\Http\Auth;

use Viktoriya\PHP2\Blog\Exceptions\AuthException;
use Viktoriya\PHP2\Blog\Exceptions\HttpException;
use Viktoriya\PHP2\Blog\Exceptions\UserNotFoundException;
use Viktoriya\PHP2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Viktoriya\PHP2\http\Request;
use Viktoriya\PHP2\Blog\User;

class JsonBodyUsernameIdentification implements IdentificationInterface
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
      return $this->usersRepository->getByUsername($username);
    } catch (UserNotFoundException $e) {
      throw new AuthException($e->getMessage());
    }
  }
}
