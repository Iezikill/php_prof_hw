<?php

namespace Viktoriya\PHP2\Http\Actions\Users;

use Viktoriya\PHP2\Blog\Exceptions\HttpException;
use Viktoriya\PHP2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Viktoriya\PHP2\Blog\Exceptions\UserNotFoundException;
use Viktoriya\PHP2\Http\Actions\ActionInterface;
use Viktoriya\PHP2\http\ErrorResponse;
use Viktoriya\PHP2\http\Request;
use Viktoriya\PHP2\http\Response;
use Viktoriya\PHP2\http\SuccessfulResponse;

class FindByUsername implements ActionInterface
{
  public function __construct(
    private UsersRepositoryInterface $usersRepository
  ) {
  }

  public function handle(Request $request): Response
  {
    try {
      $username = $request->query('username');
    } catch (HttpException $e) {
      return new ErrorResponse($e->getMessage());
    }

    try {
      $user = $this->usersRepository->getByUsername($username);
    } catch (UserNotFoundException $e) {
      return new ErrorResponse($e->getMessage());
    }

    return new SuccessfulResponse([
      'username' => $user->username(),
      'name' => $user->name()->first() . ' ' . $user->name()->last(),
    ]);
  }
}
