<?php

namespace Viktoriya\PHP2\Commands;

use Viktoriya\PHP2\Blog\Commands\Arguments;
use Viktoriya\PHP2\Blog\Commands\CreateUserCommand;
use Viktoriya\PHP2\Blog\Exceptions\ArgumentsException;
use Viktoriya\PHP2\Blog\Exceptions\CommandException;
use Viktoriya\PHP2\Blog\Exceptions\UserNotFoundException;
use Viktoriya\PHP2\Blog\Repositories\UsersRepository\DummyUsersRepository;
use Viktoriya\PHP2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Viktoriya\PHP2\Blog\User;
use Viktoriya\PHP2\Blog\UUID;
use Viktoriya\Blog\UnitTests\DummyLogger;
use PHPUnit\Framework\TestCase;

class CreateUserCommandTest extends TestCase
{
  public function testItThrowsAnExceptionWhenUserAlreadyExists(): void
  {
    $command = new CreateUserCommand(new DummyUsersRepository(), new DummyLogger());
    $this->expectException(CommandException::class);
    $this->expectExceptionMessage('User already exists: Ivan');
    $command->handle(new Arguments(['username' => 'Ivan']));
  }

  public function testItRequiresFirstName(): void
  {
    $usersRepository = new class implements UsersRepositoryInterface
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
        throw new UserNotFoundException("Not found");
      }
    };

    $command = new CreateUserCommand($usersRepository, new DummyLogger());
    $this->expectException(ArgumentsException::class);
    $this->expectExceptionMessage('No such argument: first_name');
    $command->handle(new Arguments(['username' => 'Ivan']));
  }

  public function testItRequiresLastName(): void
  {
    $command = new CreateUserCommand($this->makeUsersRepository(), new DummyLogger());
    $this->expectException(ArgumentsException::class);
    $this->expectExceptionMessage('No such argument: last_name');
    $command->handle(new Arguments([
      'username' => 'Ivan',
      'first_name' => 'Ivan',
    ]));
  }

  private function makeUsersRepository(): UsersRepositoryInterface
  {
    return new class implements UsersRepositoryInterface
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
        throw new UserNotFoundException("Not found");
      }
    };
  }

  public function testItSavesUserToRepository(): void
  {
    $usersRepository = new class implements UsersRepositoryInterface
    {
      private bool $called = false;

      public function save(User $user): void
      {
        $this->called = true;
      }

      public function get(UUID $uuid): User
      {

        throw new UserNotFoundException("Not found");
      }

      public function getByUsername(string $username): User
      {
        throw new UserNotFoundException("Not found");
      }

      public function wasCalled(): bool
      {
        return $this->called;
      }
    };

    $command = new CreateUserCommand($usersRepository, new DummyLogger());
    $command->handle(new Arguments([
      'username' => 'Ivan',
      'first_name' => 'Ivan',
      'last_name' => 'Nikitin',
    ]));

    $this->assertTrue($usersRepository->wasCalled());
  }
}
