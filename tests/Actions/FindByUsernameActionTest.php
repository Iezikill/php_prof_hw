<?php

namespace Actions;

use Viktoriya\PHP2\Blog\Exceptions\UserNotFoundException;
use Viktoriya\PHP2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Viktoriya\PHP2\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Viktoriya\PHP2\Blog\User;
use Viktoriya\PHP2\Blog\UUID;
use Viktoriya\PHP2\Http\Actions\Posts\CreatePost;
use Viktoriya\PHP2\http\Actions\Users\FindByUsername;
use Viktoriya\PHP2\http\ErrorResponse;
use Viktoriya\PHP2\http\Request;
use Viktoriya\PHP2\http\SuccessfulResponse;
use Viktoriya\PHP2\Person\Name;
use Viktoriya\Blog\UnitTests\DummyLogger;
use PHPUnit\Framework\TestCase;
use Viktoriya\PHP2\Blog\Exceptions\AuthException;
use Viktoriya\PHP2\Http\Auth\TokenAuthenticationInterface;


class FindByUsernameActionTest extends TestCase
{
  /**
   * @runInSeparateProcess
   * @preserveGlobalState disabled
   * @throws /JsonException
   */
  public function testItReturnsErrorResponseIfNoUsernameProvided(): void
  {
    $request = new Request([], [], "");
    $usersRepository = $this->usersRepository([]);
    $action = new FindByUsername($usersRepository, new DummyLogger());
    $response = $action->handle($request);
    $this->assertInstanceOf(ErrorResponse::class, $response);
    $this->expectOutputString('{"success":false,"reason":"No such query param in the request: username"}');
    $response->send();
  }
  /**
   * @runInSeparateProcess
   * @preserveGlobalState disabled
   */
  public function testItReturnsErrorResponseIfUserNotFound(): void
  {
    $request = new Request(['username' => 'ivan'], [], '');
    $usersRepository = $this->usersRepository([]);
    $action = new FindByUsername($usersRepository, new DummyLogger());
    $response = $action->handle($request);
    $this->assertInstanceOf(ErrorResponse::class, $response);
    $this->expectOutputString('{"success":false,"reason":"Not found"}');
    $response->send();
  }

  /**
   * @runInSeparateProcess
   * @preserveGlobalState disabled
   */
  public function testItReturnsSuccessfulResponse(): void
  {
    $request = new Request(['username' => 'ivan'], [], '');
    $usersRepository = $this->usersRepository([
      new User(
        UUID::random(),
        new Name('Ivan', 'Nikitin'),
        'ivan',
        '123'

      ),
    ]);
    $action = new FindByUsername($usersRepository);
    $response = $action->handle($request);
    $this->assertInstanceOf(SuccessfulResponse::class, $response);
    $this->expectOutputString('{"success":true,"data":{"username":"ivan","name":"Ivan Nikitin"}}');
    $response->send();
  }

  /**
   * @runInSeparateProcess
   * @preserveGlobalState disabled
   */
  public function testItReturnsErrorResponseIfNotFoundUser(): void
  {
    $request = new Request([], [], '{"author_uuid":"10373537-0805-4d7a-830e-22b481b4859c","title":"title","text":"text"}');
    $postsRepositoryStub = $this->createStub(PostRepositoryInterface::class);
    $authenticationStub = $this->createStub(TokenAuthenticationInterface::class);
    $authenticationStub
      ->method('user')
      ->willThrowException(
        new AuthException('Cannot find user: 10373537-0805-4d7a-830e-22b481b4859c')
      );

    $action = new CreatePost($postsRepositoryStub, new DummyLogger(), $authenticationStub);
    $response = $action->handle($request);
    $response->send();
    $this->assertInstanceOf(ErrorResponse::class, $response);
    $this->expectOutputString('{"success":false,"reason":"Cannot find user: 10373537-0805-4d7a-830e-22b481b4859c"}');
  }

  public function testItReturnsErrorResponseIfNoTextProvided(): void
  {
    $request = new Request([], [], '{"author_uuid":"10373537-0805-4d7a-830e-22b481b4859c","title":"title"}');
    $postsRepository = $this->postsRepository([]);
    $authenticationStub = $this->createStub(TokenAuthenticationInterface::class);
    $authenticationStub
      ->method('user')
      ->willReturn(
        new User(
          new UUID("10373537-0805-4d7a-830e-22b481b4859c"),
          new Name('first', 'last'),
          'username',
          '123'
        )
      );

    $action = new CreatePost($postsRepository, new DummyLogger(), $authenticationStub);
    $response = $action->handle($request);
    $this->assertInstanceOf(ErrorResponse::class, $response);
    $this->expectOutputString('{"success":false,"reason":"No such field: text"}');
    $response->send();
  }

  private function usersRepository(array $users): UsersRepositoryInterface
  {
    return new class($users) implements UsersRepositoryInterface
    {
      public function __construct(
        private array $users
      ) {
      }

      public function save(User $user): void
      {
      }

      public function get(UUID $uuid): User
      {
        throw new UserNotFoundException("Not found");
      }

      public function getByUsername(string $username): User
      {
        foreach ($this->users as $user) {
          if ($user instanceof User && $username === $user->username()) {
            return $user;
          }
        }
        throw new UserNotFoundException("Not found");
      }
    };
  }
}
