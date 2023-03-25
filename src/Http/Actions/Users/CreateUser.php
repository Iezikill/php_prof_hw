<?php

namespace Viktoriya\PHP2\Http\Actions\Users;

use Viktoriya\PHP2\Blog\Exceptions\HttpException;
use Viktoriya\PHP2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Viktoriya\PHP2\Blog\User;
use Viktoriya\PHP2\Blog\UUID;
use Viktoriya\PHP2\http\Actions\ActionInterface;
use Viktoriya\PHP2\http\ErrorResponse;
use Viktoriya\PHP2\http\Request;
use Viktoriya\PHP2\http\Response;
use Viktoriya\PHP2\http\SuccessfulResponse;
use Viktoriya\PHP2\Person\Name;

class CreateUser implements ActionInterface
{
  public function __construct(
    private UsersRepositoryInterface $usersRepository,
  ) {
  }

  public function handle(Request $request): Response
  {
    try {
      $newUserUuid = UUID::random();

      $user = new User(
        $newUserUuid,
        new Name(
          $request->jsonBodyField('first_name'),
          $request->jsonBodyField('last_name')
        ),
        $request->jsonBodyField('username')
      );
    } catch (HttpException $e) {
      return new ErrorResponse($e->getMessage());
    }

    $this->usersRepository->save($user);

    return new SuccessfulResponse([
      'uuid' => (string)$newUserUuid,
    ]);
  }
}
