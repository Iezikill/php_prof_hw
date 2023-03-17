<?php

namespace Viktoriya\PHP2\Blog\Repositories\UsersRepository;;

use Viktoriya\PHP2\Blog\Exceptions\UserNotFoundException;
use Viktoriya\PHP2\Blog\User;

class InMemoryUsersRepository
{
  private array $users = [];

  public function save(User $user): void
  {
    $this->users[] = $user;
  }

  /**
   * @param int $id
   * @return User
   * @throws UserNotFoundException
   */
  public function get(int $id): User
  {
    foreach ($this->users as $user) {
      if ($user->id() === $id) {
        return $user;
      }
    }
    throw new UserNotFoundException("User not found: $id");
  }
}
