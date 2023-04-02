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
use Psr\Log\LoggerInterface;

class CreateUser implements ActionInterface
{
  public function __construct(
    private UsersRepositoryInterface $usersRepository,
    private LoggerInterface $logger
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
        $request->jsonBodyField('username'),
        $request->jsonBodyField('password')
      );
    } catch (HttpException $e) {
      return new ErrorResponse($e->getMessage());
    }

    $this->usersRepository->save($user);
    $this->logger->info("User created: $newUserUuid");

    return new SuccessfulResponse([
      'uuid' => (string)$newUserUuid,
    ]);
  }
}
