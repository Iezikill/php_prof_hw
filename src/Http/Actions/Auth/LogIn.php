<?php

namespace Viktoriya\PHP2\Http\Actions\Auth;

use DateTimeImmutable;
use Viktoriya\PHP2\Blog\AuthToken;
use Viktoriya\PHP2\Blog\Exceptions\AuthException;
use Viktoriya\PHP2\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use Viktoriya\PHP2\Http\Actions\ActionInterface;
use Viktoriya\PHP2\Http\Auth\PasswordAuthenticationInterface;
use Viktoriya\PHP2\http\Request;
use Viktoriya\PHP2\Http\ErrorResponse;
use Viktoriya\PHP2\http\Response;
use Viktoriya\PHP2\Http\SuccessfulResponse;

class LogIn implements ActionInterface
{
  public function __construct(
    private PasswordAuthenticationInterface $passwordAuthentication,
    private AuthTokensRepositoryInterface $authTokensRepository
  ) {
  }

  public function handle(Request $request): Response
  {
    try {
      $user = $this->passwordAuthentication->user($request);
    } catch (AuthException $e) {
      return new ErrorResponse($e->getMessage());
    }
    $authToken = new AuthToken(
      bin2hex(random_bytes(40)),
      $user->uuid(),
      (new DateTimeImmutable())->modify('+1 day')
    );
    $this->authTokensRepository->save($authToken);
    return new SuccessfulResponse([
      'token' => $authToken->token(),
    ]);
  }
}
