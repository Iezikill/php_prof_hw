<?php

namespace Viktoriya\PHP2\Http\Actions\Auth;

use DateTimeImmutable;
use Viktoriya\PHP2\Blog\AuthToken;
use Viktoriya\PHP2\Blog\Exceptions\AuthException;
use Viktoriya\PHP2\Blog\Exceptions\HttpException;
use Viktoriya\PHP2\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use Viktoriya\PHP2\Http\Actions\ActionInterface;
use Viktoriya\PHP2\Http\Auth\PasswordAuthenticationInterface;
use Viktoriya\PHP2\http\Request;
use Viktoriya\PHP2\Http\ErrorResponse;
use Viktoriya\PHP2\http\Response;
use Viktoriya\PHP2\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class LogOut implements ActionInterface
{
  public function __construct(
    private PasswordAuthenticationInterface $passwordAuthentication,
    private AuthTokensRepositoryInterface $authTokensRepository
  ) {
  }

  /**
   * @param Request $request
   * @return Response
   */
  public function handle(Request $request): Response
  {
    $container = require 'bootstrap.php';
    $logger = $container->get(LoggerInterface::class);

    try {
      $user = $this->passwordAuthentication->user($request);
    } catch (AuthException $e) {
      return new ErrorResponse($e->getMessage());
    }

    try {
      $token = $request->query('token');
    } catch (HttpException $e) {
      $logger->warning($e->getMessage());
      return new ErrorResponse($e->getMessage());
    }

    $this->authTokensRepository->get($token);
    $updatedToken = new AuthToken(
      $token,
      $user->uuid(),
      (new DateTimeImmutable())->modify('-1 day')
    );
    $this->authTokensRepository->save($updatedToken);
    return new SuccessfulResponse([
      'token' => $updatedToken->token(),
    ]);
  }
}
