<?php

namespace Viktoriya\PHP2\Blog\Repositories\UsersRepository;;

use Viktoriya\PHP2\Blog\Exceptions\UserNotFoundException;
use Viktoriya\PHP2\Blog\User;

class InMemoryUsersRepository implements UsersRepositoryInterface
{

  private array $users = [];
  public function save(User $user): void
  {
    $this->users[] = $user;
  }
  public function get(UUID $uuid): User
  {
    foreach ($this->users as $user) {
      if ((string)$user->uuid() === (string)$uuid) {
        return $user;
      }
    }
    throw new UserNotFoundException("User not found: $uuid");
  }
}
