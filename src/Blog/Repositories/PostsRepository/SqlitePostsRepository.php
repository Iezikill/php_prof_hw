<?php

namespace Viktoriya\PHP2\Blog\Repositories\PostsRepository;

use Viktoriya\PHP2\Blog\Exceptions\InvalidArgumentException;
use Viktoriya\PHP2\Blog\Exceptions\PostNotFoundException;
use Viktoriya\PHP2\Blog\Post;
use Viktoriya\PHP2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Viktoriya\PHP2\Blog\UUID;
use PDO;

class SqlitePostsRepository implements PostsRepositoryInterface
{
  private PDO $connection;

  public function __construct(PDO $connection)
  {
    $this->connection = $connection;
  }

  public function save(Post $post): void
  {
    $statement = $this->connection->prepare(

      'INSERT INTO posts (uuid, author_uuid, title, text) VALUES (:uuid, :author_uuid, :title, :text)'
    );
    $statement->execute([
      ':uuid' => $post->getUuid(),
      ':author_uuid' => $post->getUser()->uuid(),
      ':title' => $post->getTitle(),
      ':text' => $post->getText()
    ]);
  }

  /**
   * @throws InvalidArgumentException
   * @throws PostNotFoundException
   */
  public function get(UUID $uuid): Post
  {

    $statement = $this->connection->prepare(
      'SELECT * FROM posts WHERE uuid = :uuid'
    );
    $statement->execute([
      ':uuid' => (string)$uuid,
    ]);
    return $this->getPost($statement, $uuid);
  }

  /**
   * @throws PostNotFoundException
   * @throws InvalidArgumentException
   */
  private function getPost(\PDOStatement $statement, string $postUuId): Post
  {
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    if (false === $result) {
      throw new PostNotFoundException(
        "Cannot find post: $postUuId"
      );
    }
    $userRepository = new SqliteUsersRepository($this->connection);
    $user = $userRepository->get(new UUID($result['author_uuid']));
    return new Post(
      new UUID($result['uuid']),
      $user,
      $result['title'],
      $result['text']
    );
  }
}
