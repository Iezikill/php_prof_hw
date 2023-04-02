<?php

namespace Viktoriya\PHP2\Blog\Repositories\LikeRepository;

use Viktoriya\PHP2\Blog\Exceptions\LikeNotFoundException;
use Viktoriya\PHP2\Blog\Exceptions\LikeAlreadyExist;
use Viktoriya\PHP2\Blog\Like;

use Viktoriya\PHP2\Blog\Post;
use Viktoriya\PHP2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Viktoriya\PHP2\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Viktoriya\PHP2\Blog\User;
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


  public function getByPostUuid(UUID $postUuid): array
  {
    $statement = $this->connection->prepare(
      'SELECT * FROM likes WHERE post_uuid = :post_uuid'
    );

    $statement->execute([':post_uuid' => (string)$postUuid]);
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    if (!$result) {
      throw new LikeNotFoundException(
        'No likes to post with uuid = : ' . $postUuid
      );
    }

    $userRepository = new SqliteUsersRepository($this->connection);
    $postRepository = new SqlitePostsRepository($this->connection);
    $likes = [];
    foreach ($result as $like) {
      print_r($like);
      $user = $userRepository->get(new UUID($like['author_uuid']));
      $post = $postRepository->get(new UUID($like['post_uuid']));
      $likes[] = new Like(
        uuid: new UUID($like['uuid']),
        post: $post,
        user: $user,
      );
    }
    return $likes;
  }
}
