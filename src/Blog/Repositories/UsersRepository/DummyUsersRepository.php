<?php

namespace Viktoriya\PHP2\Blog\Repositories\UsersRepository;

use Viktoriya\PHP2\Blog\Exceptions\UserNotFoundException;
use Viktoriya\PHP2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Viktoriya\PHP2\Blog\User;
use Viktoriya\PHP2\Blog\UUID;
use Viktoriya\PHP2\Person\Name;

class DummyUsersRepository implements UsersRepositoryInterface
{

  public function save(User $user): void
  {
  }

  public function get(UUID $uuid): User
  {
    throw new UserNotFoundException("Not found");
  }

  public function getByUsername(string $username): User
  {
    return new User(UUID::random(), new Name("first", "last"), "user123");
  }
}
