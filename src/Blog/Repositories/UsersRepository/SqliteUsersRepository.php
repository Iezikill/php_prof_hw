<?php

namespace Viktoriya\PHP2\Blog\Repositories\UsersRepository;

use Viktoriya\PHP2\Blog\Exceptions\InvalidArgumentException;
use Viktoriya\PHP2\Blog\Exceptions\UserNotFoundException;
use Viktoriya\PHP2\Blog\User;
use Viktoriya\PHP2\Blog\UUID;
use Viktoriya\PHP2\Person\Name;
use \PDO;
use PDOStatement;

class SqliteUsersRepository implements UsersRepositoryInterface
{
  private PDO $connection;

  public function __construct(PDO $connection)
  {
    $this->connection = $connection;
  }

  public function save(User $user): void
  {
    $statement = $this->connection->prepare(
      'INSERT INTO users (
                   first_name, 
                   last_name, 
                   uuid, 
                   username) 
            VALUES (
                    :first_name, 
                    :last_name, 
                    :uuid, 
                    :username
                    )
                    ON CONFLICT(uuid) DO UPDATE SET
                    first_name=:first_name,
                    last_name=:last_name
                    '
    );
    $statement->execute([
      ':first_name' => $user->name()->first(),
      ':last_name' => $user->name()->last(),
      ':uuid' => (string)$user->uuid(),
      ':username' => $user->username()
    ]);
  }

  /**
   * @throws InvalidArgumentException
   * @throws UserNotFoundException
   */
  public function get(UUID $uuid): User
  {
    $statement = $this->connection->prepare(
      'SELECT * FROM users WHERE uuid = :uuid'
    );
    $statement->execute([
      ':uuid' => (string)$uuid,
    ]);
    return $this->getUser($statement, $uuid);
  }

  /**
   * @throws InvalidArgumentException
   * @throws UserNotFoundException
   */
  public function getByUsername(string $username): User
  {
    $statement = $this->connection->prepare(
      'SELECT * FROM users WHERE username = :username'
    );
    $statement->execute([
      ':username' => $username,
    ]);
    return $this->getUser($statement, $username);
  }

  /**
   * @throws InvalidArgumentException
   * @throws UserNotFoundException
   */
  private function getUser(PDOStatement $statement, string $username): User
  {
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    if (false === $result) {
      throw new UserNotFoundException(
        "Cannot find user: $username"
      );
    }
    return new User(
      new UUID($result['uuid']),
      new Name($result['first_name'], $result['last_name']),
      $result['username'],
    );
  }
}
