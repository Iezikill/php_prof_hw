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
use Psr\Log\LoggerInterface;

class DeletePost implements ActionInterface
{
  public function __construct(
    private PostsRepositoryInterface $postsRepository,
    private LoggerInterface $logger
  ) {
  }

  public function handle(Request $request): Response
  {
    $container = require 'bootstrap.php';
    $logger = $container->get(LoggerInterface::class);

    try {
      $postUuid = $request->query('uuid');
      $this->postsRepository->get(new UUID($postUuid));
    } catch (PostNotFoundException $error) {
      $logger->warning($error->getMessage());
      return new ErrorResponse($error->getMessage());
    }

    $this->postsRepository->delete(new UUID($postUuid));
    $logger->info("Post deleted: $postUuid");

    return new SuccessfulResponse([
      'uuid' => $postUuid,
    ]);
  }
}
