<?php

namespace Viktoriya\PHP2\Blog\Repositories\AuthTokensRepository;

use Viktoriya\PHP2\Blog\AuthToken;

interface AuthTokensRepositoryInterface
{
  public function save(AuthToken $authToken): void;
  public function get(string $token): AuthToken;
}
