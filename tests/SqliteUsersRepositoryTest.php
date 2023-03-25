<?php

namespace Viktoriya\PHP2;

use Viktoriya\PHP2\Blog\Exceptions\UserNotFoundException;
use Viktoriya\PHP2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Viktoriya\PHP2\Blog\User;
use Viktoriya\PHP2\Blog\UUID;
use Viktoriya\PHP2\Person\Name;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class SqliteUsersRepositoryTest extends TestCase
{
  public function testItThrowsAnExceptionWhenUserNotFound(): void
  {
    $connectionMock = $this->createStub(PDO::class);
    $statementStub = $this->createStub(PDOStatement::class);
    $statementStub->method('fetch')->willReturn(false);
    $connectionMock->method('prepare')->willReturn($statementStub);
    $repository = new SqliteUsersRepository($connectionMock);
    $this->expectException(UserNotFoundException::class);
    $this->expectExceptionMessage('Cannot find user: Stan');
    $repository->getByUsername('Stan');
  }

  public function testItSavesUserToDatabase(): void
  {
    $connectionStub = $this->createStub(PDO::class);
    $statementMock = $this->createMock(PDOStatement::class);
    $statementMock
      ->expects($this->once())
      ->method('execute')
      ->with([
        ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
        ':username' => 'ivan1',
        ':first_name' => 'Ivan',
        ':last_name' => 'Ivanov',
      ]);
    $connectionStub->method('prepare')->willReturn($statementMock);
    $repository = new SqliteUsersRepository($connectionStub);
    $repository->save(
      new User(
        new UUID('123e4567-e89b-12d3-a456-426614174000'),
        new Name('Ivan', 'Ivanov'),
        'ivan1',
      )
    );
  }
}
