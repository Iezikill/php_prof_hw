<?php

namespace Viktoriya\PHP2\Http\Actions\Comments;

use Viktoriya\PHP2\Blog\Comment;
use Viktoriya\PHP2\Blog\Exceptions\InvalidArgumentException;
use Viktoriya\PHP2\Blog\Exceptions\PostNotFoundException;
use Viktoriya\PHP2\Blog\Exceptions\UserNotFoundException;
use Viktoriya\PHP2\Blog\Exceptions\AuthException;
use Viktoriya\PHP2\Blog\Post;
use Viktoriya\PHP2\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use Viktoriya\PHP2\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Viktoriya\PHP2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Viktoriya\PHP2\Blog\UUID;
use Viktoriya\PHP2\Blog\Exceptions\HttpException;
use Viktoriya\PHP2\http\Actions\ActionInterface;
use Viktoriya\PHP2\Http\Auth\TokenAuthenticationInterface;
use Viktoriya\PHP2\http\ErrorResponse;
use Viktoriya\PHP2\http\Request;
use Viktoriya\PHP2\http\Response;
use Viktoriya\PHP2\http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class CreateComment implements ActionInterface
{
  public function __construct(
    private PostsRepositoryInterface $postsRepository,
    private CommentsRepositoryInterface $commentsRepository,
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
      $logger->warning($e->getMessage());
      return new ErrorResponse($exception->getMessage());
    }

    try {
      $post = $this->postsRepository->get($postUuid);
    } catch (PostNotFoundException $exception) {
      $logger->warning($e->getMessage());
      return new ErrorResponse($exception->getMessage());
    }

    $newCommentUuid = UUID::random();

    try {
      $comment = new Comment(
        $newCommentUuid,
        $user,
        $post,
        $request->jsonBodyField('text'),
      );
    } catch (HttpException $exception) {
      return new ErrorResponse($exception->getMessage());
    }

    $this->commentsRepository->save($comment);
    $this->logger->info("Comment created: $newCommentUuid");
    return new SuccessfulResponse([
      'uuid' => (string)$newCommentUuid,
    ]);
  }
}
