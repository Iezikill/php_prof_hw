<?php

namespace Viktoriya\PHP2\Http\Actions\Posts;

use Viktoriya\PHP2\Blog\Exceptions\InvalidArgumentException;
use Viktoriya\PHP2\Blog\Exceptions\UserNotFoundException;
use Viktoriya\PHP2\Blog\Post;
use Viktoriya\PHP2\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Viktoriya\PHP2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Viktoriya\PHP2\Blog\UUID;
use Viktoriya\PHP2\Blog\Exceptions\HttpException;
use Viktoriya\PHP2\http\Actions\ActionInterface;
use Viktoriya\PHP2\http\ErrorResponse;
use Viktoriya\PHP2\http\Request;
use Viktoriya\PHP2\http\Response;
use Viktoriya\PHP2\http\SuccessfulResponse;
use Psr\Log\LoggerInterface;


class CreatePost implements ActionInterface
{

  public function __construct(
    private UsersRepositoryInterface $usersRepository,
    private PostsRepositoryInterface $postsRepository,
    private LoggerInterface $logger
  ) {
  }

  public function handle(Request $request): Response
  {
    try {
      $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
    } catch (HttpException | InvalidArgumentException $exception) {
      return new ErrorResponse($exception->getMessage());
    }

    try {
      $user = $this->usersRepository->get($authorUuid);
    } catch (UserNotFoundException $exception) {
      return new ErrorResponse($exception->getMessage());
    }

    $newPostUuid = UUID::random();

    try {
      $post = new Post(
        $newPostUuid,
        $user,
        $request->jsonBodyField('title'),
        $request->jsonBodyField('text'),
      );
    } catch (HttpException $exception) {
      return new ErrorResponse($exception->getMessage());
    }

    $this->postsRepository->save($post);
    $this->logger->info("Post created: $newPostUuid");

    return new SuccessfulResponse([
      'uuid' => (string)$newPostUuid,
    ]);
  }
}
