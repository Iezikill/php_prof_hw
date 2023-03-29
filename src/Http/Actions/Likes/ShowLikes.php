<?php

namespace Viktoriya\PHP2\Http\Actions\Likes;

use Viktoriya\PHP2\Blog\Exceptions\HttpException;
use Viktoriya\PHP2\Blog\Exceptions\InvalidArgumentException;
use Viktoriya\PHP2\Blog\Exceptions\LikeNotFoundException;
use Viktoriya\PHP2\Blog\Repositories\LikeRepository\LikeRepositoryInterface;
use Viktoriya\PHP2\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Viktoriya\PHP2\Blog\UUID;
use Viktoriya\PHP2\Http\Actions\ActionInterface;
use Viktoriya\PHP2\Http\ErrorResponse;
use Viktoriya\PHP2\Http\Request;
use Viktoriya\PHP2\Http\Response;
use Viktoriya\PHP2\Http\SuccessfulResponse;

class ShowLikes implements ActionInterface
{
  public function __construct(
    private PostsRepositoryInterface $postsRepository,
    private LikeRepositoryInterface $likeRepository
  ) {
  }

  public function handle(Request $request): Response
  {
    try {
      $postUuid = new UUID($request->query('post_uuid'));
    } catch (HttpException | InvalidArgumentException $exception) {
      return new ErrorResponse($exception->getMessage());
    }

    try {
      $like = $this->likeRepository->getByPostUuid($postUuid);
    } catch (LikeNotFoundException $exception) {
      return new ErrorResponse($exception->getMessage());
    }

    return new SuccessfulResponse([
      ':uuid' => $like->getUuid(),
      ':post_uuid' => $like->getPost()->getUuid(),
      ':author_uuid' => $like->getUser()->uuid(),
    ]);
  }
}
