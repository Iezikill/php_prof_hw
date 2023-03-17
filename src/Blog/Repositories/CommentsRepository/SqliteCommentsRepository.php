<?php

namespace Viktoriya\PHP2\Blog\Repositories\CommentsRepository;

use Viktoriya\PHP2\Blog\Comment;
use Viktoriya\PHP2\Blog\Exceptions\CommentNotFoundException;
use Viktoriya\PHP2\Blog\Exceptions\InvalidArgumentException;
use Viktoriya\PHP2\Blog\Exceptions\PostNotFoundException;
use Viktoriya\PHP2\Blog\Exceptions\UserNotFoundException;
use Viktoriya\PHP2\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Viktoriya\PHP2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Viktoriya\PHP2\Blog\UUID;
use PDO;

class SqliteCommentsRepository implements CommentsRepositoryInterface
{
  private PDO $connection;

  public function __construct(PDO $connection)
  {
    $this->connection = $connection;
  }

  public function save(Comment $comment): void
  {
    $statement = $this->connection->prepare(

      'INSERT INTO comments (uuid, post_uuid, author_uuid, text) VALUES (:uuid, :post_uuid, :author_uuid, :text)'
    );
    $statement->execute([
      ':uuid' => $comment->getUuid(),
      ':post_uuid' => $comment->getPost()->getUuid(),
      ':author_uuid' => $comment->getUser()->uuid(),
      ':text' => $comment->getCommentText()
    ]);
  }

  public function get(UUID $uuid): Comment
  {
    $statement = $this->connection->prepare(
      'SELECT * FROM comments WHERE uuid = :uuid'
    );
    $statement->execute([
      ':uuid' => (string)$uuid,
    ]);
    return $this->getComment($statement, $uuid);
  }

  /**
   * @throws InvalidArgumentException
   * @throws CommentNotFoundException
   * @throws UserNotFoundException
   * @throws PostNotFoundException
   */
  private function getComment(\PDOStatement $statement, string $commentUuId): Comment
  {
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    if (false === $result) {
      throw new CommentNotFoundException(
        "Cannot find comment: $commentUuId"
      );
    }
    $userRepository = new SqliteUsersRepository($this->connection);
    $user = $userRepository->get(new UUID($result['author_uuid']));
    $postRepository = new SqlitePostsRepository($this->connection);
    $post = $postRepository->get(new UUID($result['post_uuid']));

    return new Comment(
      new UUID($result['uuid']),
      $post,
      $user,
      $result['text']
    );
  }
}
