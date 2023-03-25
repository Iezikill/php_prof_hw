<?php

namespace Viktoriya\PHP2\Http\Actions\Posts;

use Viktoriya\PHP2\Blog\Exceptions\PostNotFoundException;
use Viktoriya\PHP2\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Viktoriya\PHP2\Blog\UUID;
use Viktoriya\PHP2\Http\Actions\ActionInterface;
use Viktoriya\PHP2\Http\ErrorResponse;
use Viktoriya\PHP2\Http\SuccessfulResponse;
use Viktoriya\PHP2\http\Request;
use Viktoriya\PHP2\http\Response;

class DeletePost implements ActionInterface
{
  public function __construct(
    private PostsRepositoryInterface $postsRepository
  ) {
  }


  public function handle(Request $request): Response
  {
    try {
      $postUuid = $request->query('uuid');
      $this->postsRepository->get(new UUID($postUuid));
    } catch (PostNotFoundException $error) {
      return new ErrorResponse($error->getMessage());
    }

    $this->postsRepository->delete(new UUID($postUuid));

    return new SuccessfulResponse([
      'uuid' => $postUuid,
    ]);
  }
}
