<?php

namespace Viktoriya\PHP2\Http\Auth;

use DateTimeImmutable;
use Viktoriya\PHP2\Blog\Exceptions\AuthException;
use Viktoriya\PHP2\Blog\Exceptions\AuthTokenNotFoundException;
use Viktoriya\PHP2\Blog\Exceptions\HttpException;
use Viktoriya\PHP2\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use Viktoriya\PHP2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Viktoriya\PHP2\Blog\User;
use Viktoriya\PHP2\Http\Request;

class BearerTokenAuthentication implements TokenAuthenticationInterface
{

  private const HEADER_PREFIX = 'Bearer ';

  public function __construct(
    private AuthTokensRepositoryInterface $authTokensRepository,
    private UsersRepositoryInterface $usersRepository,
  ) {
  }

  /**
   * @throws AuthException
   */
  public function user(Request $request): User
  {
    try {
      $header = $request->header('Authorization');
    } catch (HttpException $e) {
      throw new AuthException($e->getMessage());
    }
    if (!str_starts_with($header, self::HEADER_PREFIX)) {
      throw new AuthException("Malformed token: [$header]");
    }
    $token = mb_substr($header, strlen(self::HEADER_PREFIX));
    try {
      $authToken = $this->authTokensRepository->get($token);
    } catch (AuthTokenNotFoundException) {
      throw new AuthException("Bad token: [$token]");
    }
    if ($authToken->expiresOn() <= new DateTimeImmutable()) {
      throw new AuthException("Token expired: [$token]");
    }
    $userUuid = $authToken->userUuid();
    return $this->usersRepository->get($userUuid);
  }
}
