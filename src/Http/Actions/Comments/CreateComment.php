<?php

namespace Viktoriya\PHP2\Http\Actions\Comments;

use Viktoriya\PHP2\Blog\Comment;
use Viktoriya\PHP2\Blog\Exceptions\InvalidArgumentException;
use Viktoriya\PHP2\Blog\Exceptions\PostNotFoundException;
use Viktoriya\PHP2\Blog\Exceptions\UserNotFoundException;
use Viktoriya\PHP2\Blog\Post;
use Viktoriya\PHP2\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use Viktoriya\PHP2\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Viktoriya\PHP2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Viktoriya\PHP2\Blog\UUID;
use Viktoriya\PHP2\Blog\Exceptions\HttpException;
use Viktoriya\PHP2\http\Actions\ActionInterface;
use Viktoriya\PHP2\http\ErrorResponse;
use Viktoriya\PHP2\http\Request;
use Viktoriya\PHP2\http\Response;
use Viktoriya\PHP2\http\SuccessfulResponse;

class CreateComment implements ActionInterface
{
  public function __construct(
    private UsersRepositoryInterface $usersRepository,
    private PostsRepositoryInterface $postsRepository,
    private CommentsRepositoryInterface $commentsRepository
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

    try {
      $postUuid = new UUID($request->jsonBodyField('post_uuid'));
    } catch (HttpException | InvalidArgumentException $exception) {
      return new ErrorResponse($exception->getMessage());
    }

    try {
      $post = $this->postsRepository->get($postUuid);
    } catch (PostNotFoundException $exception) {
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

    return new SuccessfulResponse([
      'uuid' => (string)$newCommentUuid,
    ]);
  }
}
