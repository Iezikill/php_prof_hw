<?php

namespace Viktoriya\PHP2\Http\Auth;

use Viktoriya\PHP2\Blog\Exceptions\AuthException;
use Viktoriya\PHP2\Blog\Exceptions\HttpException;
use Viktoriya\PHP2\Blog\Exceptions\InvalidArgumentException;
use Viktoriya\PHP2\Blog\Exceptions\UserNotFoundException;
use Viktoriya\PHP2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Viktoriya\PHP2\Blog\User;
use Viktoriya\PHP2\Blog\UUID;
use Viktoriya\PHP2\Http\Request;

class JsonBodyUuidIdentification implements IdentificationInterface
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
      $userUuid = new UUID($request->jsonBodyField('user_uuid'));
    } catch (HttpException | InvalidArgumentException $e) {
      throw new AuthException($e->getMessage());
    }

    try {
      return $this->usersRepository->get($userUuid);
    } catch (UserNotFoundException $e) {
      throw new AuthException($e->getMessage());
    }
  }
}
