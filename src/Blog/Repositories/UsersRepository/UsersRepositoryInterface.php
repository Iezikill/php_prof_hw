<?php

namespace Viktoriya\PHP2\Blog\Repositories\UsersRepository;

use Viktoriya\PHP2\Blog\User;
use Viktoriya\PHP2\Blog\UUID;

interface UsersRepositoryInterface
{
  public function save(User $user): void;
  public function get(UUID $uuid): User;
  public function getByUsername(string $username): User;
}
