<?php

namespace Viktoriya\PHP2\Http\Actions\Comments;

use Viktoriya\PHP2\Blog\Exceptions\CommentNotFoundException;
use Viktoriya\PHP2\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use Viktoriya\PHP2\Blog\UUID;
use Viktoriya\PHP2\Http\Actions\ActionInterface;
use Viktoriya\PHP2\Http\ErrorResponse;
use Viktoriya\PHP2\Http\SuccessfulResponse;
use Viktoriya\PHP2\http\Request;
use Viktoriya\PHP2\http\Response;
use Psr\Log\LoggerInterface;

class DeleteComment implements ActionInterface
{
  public function __construct(
    private CommentsRepositoryInterface $commentsRepository,
    private LoggerInterface $logger
  ) {
  }

  public function handle(Request $request): Response
  {
    $container = require 'bootstrap.php';
    $logger = $container->get(LoggerInterface::class);

    try {
      $commentUuid = $request->query('uuid');
      $this->commentsRepository->get(new UUID($commentUuid));
    } catch (CommentNotFoundException $error) {
      return new ErrorResponse($error->getMessage());
    }

    $this->commentsRepository->delete(new UUID($commentUuid));
    $logger->info("Comment deleted: $commentUuid");

    return new SuccessfulResponse([
      'uuid' => $commentUuid,
    ]);
  }
}
