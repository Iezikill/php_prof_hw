<?php

namespace Viktoriya\PHP2\Http\Actions\Likes;

use Viktoriya\PHP2\Blog\Exceptions\InvalidArgumentException;
use Viktoriya\PHP2\Blog\Exceptions\PostNotFoundException;
use Viktoriya\PHP2\Blog\Exceptions\LikeAlreadyExist;
use Viktoriya\PHP2\Blog\Exceptions\UserNotFoundException;
use Viktoriya\PHP2\Blog\Exceptions\AuthException;
use Viktoriya\PHP2\Blog\Like;
use Viktoriya\PHP2\Blog\Repositories\LikeRepository\LikeRepositoryInterface;
use Viktoriya\PHP2\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Viktoriya\PHP2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Viktoriya\PHP2\Blog\UUID;
use Viktoriya\PHP2\Blog\Exceptions\HttpException;
use Viktoriya\PHP2\http\Actions\ActionInterface;
use Viktoriya\PHP2\http\ErrorResponse;
use Viktoriya\PHP2\http\Request;
use Viktoriya\PHP2\http\Response;
use Viktoriya\PHP2\http\SuccessfulResponse;
use Viktoriya\PHP2\Http\Auth\TokenAuthenticationInterface;
use Psr\Log\LoggerInterface;

class CreateLike implements ActionInterface
{
  public function __construct(
    private UsersRepositoryInterface $usersRepository,
    private PostsRepositoryInterface $postsRepository,
    private LikeRepositoryInterface $likeRepository,
    private LoggerInterface $logger,
    private TokenAuthenticationInterface $authentication
  ) {
  }

  public function handle(Request $request): Response
  {
    $container = require 'bootstrap.php';
    $logger = $container->get(LoggerInterface::class);

    try {
      $user = $this->authentication->user($request);
    } catch (AuthException $e) {
      $logger->warning($e->getMessage());
      return new ErrorResponse($e->getMessage());
    }

    try {
      $postUuid = new UUID($request->jsonBodyField('post_uuid'));
    } catch (HttpException | InvalidArgumentException $exception) {
      $logger->warning($exception->getMessage());
      return new ErrorResponse($exception->getMessage());
    }

    try {
      $post = $this->postsRepository->get($postUuid);
    } catch (PostNotFoundException $exception) {
      $logger->warning($exception->getMessage());
      return new ErrorResponse($exception->getMessage());
    }

    $newLikeUuid = UUID::random();

    try {
      $like = new Like(
        $newLikeUuid,
        $post,
        $user,
      );
    } catch (HttpException $exception) {
      return new ErrorResponse($exception->getMessage());
    }

    $this->likeRepository->save($like);
    $this->logger->info("Like created: $newLikeUuid");
    return new SuccessfulResponse([
      'uuid' => (string)$newLikeUuid,
    ]);
  }
}
