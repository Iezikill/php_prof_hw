<?php

namespace Viktoriya\PHP2\Blog\Commands;

use Viktoriya\PHP2\Blog\Exceptions\CommandException;
use Viktoriya\PHP2\Blog\Exceptions\ArgumentsException;
use Viktoriya\PHP2\Blog\Exceptions\UserNotFoundException;
use Viktoriya\PHP2\Blog\Exceptions\InvalidArgumentException;
use Viktoriya\PHP2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Viktoriya\PHP2\Blog\User;
use Viktoriya\PHP2\Blog\UUID;
use Viktoriya\PHP2\Person\Name;
use Psr\Log\LoggerInterface;

final class CreateUserCommand
{
  public function __construct(
    private UsersRepositoryInterface $usersRepository,
    private LoggerInterface $logger
  ) {
  }

  public function handle(Arguments $arguments): void
  {
    $this->logger->info("Create user command started");
    $username = $arguments->get('username');
    if ($this->userExists($username)) {
      $this->logger->warning("User already exists: $username");
      throw new CommandException("User already exists: $username");
    }

    $uuid = UUID::random();
    $this->usersRepository->save(new User(
      $uuid,
      new Name(
        $arguments->get('first_name'),
        $arguments->get('last_name')
      ),
      $username,
    ));
    $this->logger->info("User created: $uuid");
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
