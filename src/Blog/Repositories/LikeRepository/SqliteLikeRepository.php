<?php

namespace Viktoriya\PHP2\Blog\Repositories\LikeRepository;

use Viktoriya\PHP2\Blog\Exceptions\LikeNotFoundException;
use Viktoriya\PHP2\Blog\Like;

use Viktoriya\PHP2\Blog\Post;
use Viktoriya\PHP2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Viktoriya\PHP2\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Viktoriya\PHP2\Blog\UUID;
use \PDO;
use \PDOStatement;

class SqliteLikeRepository implements LikeRepositoryInterface
{
  private PDO $connection;

  public function __construct(PDO $connection)
  {
    $this->connection = $connection;
  }


  public function save(Like $like): void
  {
    $statement = $this->connection->prepare(
      'INSERT INTO likes (uuid, post_uuid, author_uuid) 
            VALUES (:uuid, :post_uuid, :author_uuid)'
    );

    $statement->execute([
      ':uuid' => $like->getUuid(),
      ':post_uuid' => $like->getPost()->getUuid(),
      ':author_uuid' => $like->getUser()->uuid(),
    ]);
  }


  public function getByPostUuid(UUID $postUuid): Like
  {
    $statement = $this->connection->prepare(
      'SELECT * FROM likes WHERE post_uuid = :post_uuid'
    );

    $statement->execute([':post_uuid' => (string)$postUuid]);

    return $this->getLike($statement, $postUuid);
  }


  private function getLike(PDOStatement $statement, string $postUuid): Like
  {
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    if ($result === false) {
      throw new LikeNotFoundException(
        "Cannot find like from post: $postUuid"
      );
    }

    $userRepository = new SqliteUsersRepository($this->connection);
    $user = $userRepository->get(new UUID($result['author_uuid']));

    $postRepository = new SqlitePostsRepository($this->connection);
    $post = $postRepository->get(new UUID($result['post_uuid']));

    return new Like(
      new UUID($result['uuid']),
      $post,
      $user,
    );
  }
}
