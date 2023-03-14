<?php

namespace Viktoriya\PHP2\Blog\Commands;

use Viktoriya\PHP2\Blog\Exceptions\CommandException;
use Viktoriya\PHP2\Blog\Exceptions\UserNotFoundException;
use Viktoriya\PHP2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Viktoriya\PHP2\Blog\User;
use Viktoriya\PHP2\Blog\UUID;
use Viktoriya\PHP2\Person\Name;

final class CreateUserCommand
{
  public function __construct(
    private UsersRepositoryInterface $usersRepository
  ) {
  }
  public function handle(Arguments $arguments): void
  {
    $username = $arguments->get('username');
    if ($this->userExists($username)) {
      throw new CommandException("User already exists: $username");
    }
    $this->usersRepository->save(new User(
      UUID::random(),
      new Name($arguments->get('first_name'), $arguments->get('last_name')),
      $username,
    ));
  }
  private function userExists(string $username): bool
  {
    try {
      $this->usersRepository->getByUsername($username);
    } catch (UserNotFoundException) {
      return false;
    }
    return true;
  }
}
