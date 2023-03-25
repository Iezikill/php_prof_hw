<?php

namespace Viktoriya\PHP2;

use Viktoriya\PHP2\Blog\Post;
use Viktoriya\PHP2\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Viktoriya\PHP2\Blog\Exceptions\PostNotFoundException;
use Viktoriya\PHP2\Blog\User;
use Viktoriya\PHP2\Blog\UUID;
use Viktoriya\PHP2\Person\Name;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class SqlitePostRepositoryTest extends TestCase
{
  public function testItThrowsAnExceptionWhenPostNotFound(): void
  {
    $connectionMock = $this->createStub(PDO::class);
    $statementStub = $this->createStub(PDOStatement::class);

    $statementStub->method('fetch')->willReturn(false);
    $connectionMock->method('prepare')->willReturn($statementStub);

    $repository = new SqlitePostsRepository($connectionMock);

    $this->expectException(PostNotFoundException::class);
    $this->expectExceptionMessage('Cannot find post: 123e4567-e89b-12d3-a456-426614174025');

    $repository->get(new UUID('123e4567-e89b-12d3-a456-426614174025'));
  }

  public function testItSavesPostToDatabase(): void
  {
    $connectionStub = $this->createStub(PDO::class);
    $statementMock = $this->createMock(PDOStatement::class);

    $statementMock
      ->expects($this->once())
      ->method('execute')
      ->with([
        ':uuid' => '123e4567-e89b-12d3-a456-426614174025',
        ':author_uuid' => '123e4567-e89b-12d3-a456-426614174000',
        ':title' => 'Title',
        ':text' => 'Text',
      ]);

    $connectionStub->method('prepare')->willReturn($statementMock);

    $repository = new SqlitePostsRepository($connectionStub);

    $user = new User(
      new UUID('123e4567-e89b-12d3-a456-426614174000'),
      new Name('first_name', 'last_name'),
      'ivan1',
    );

    $repository->save(
      new Post(
        new UUID('123e4567-e89b-12d3-a456-426614174025'),
        $user,
        'Title',
        'Text'
      )
    );
  }

  public function testItGetPostByUuid(): void
  {
    $connectionStub = $this->createStub(\PDO::class);
    $statementMock = $this->createMock(\PDOStatement::class);

    $statementMock
      ->expects($this->once())
      ->method('execute')
      ->with([
        ':uuid' => '9dba7ab0-93be-4ff4-9699-165320c97694',
        ':author_uuid' => '123e4567-e89b-12d3-a456-426614174000',
        ':title' => 'Title',
        ':text' => 'Text',
        ':username' => 'ivan1',
        ':first_name' => 'Ivan',
        ':last_name' => 'Ivanov'
      ]);
    $connectionStub->method('prepare')->willReturn($statementMock);

    $postRepository = new SqlitePostsRepository($connectionStub);

    $post = $postRepository->get(new UUID('9dba7ab0-93be-4ff4-9699-165320c97694'));

    $this->assertSame('9dba7ab0-93be-4ff4-9699-165320c97694', (string)$post->getUuid());
  }
}
