<?php

namespace Viktoriya\PHP2\Http\Actions\Posts;

use Viktoriya\PHP2\Blog\Exceptions\InvalidArgumentException;
use Viktoriya\PHP2\Blog\Exceptions\UserNotFoundException;
use Viktoriya\PHP2\Blog\Exceptions\AuthException;
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
use Viktoriya\PHP2\Http\Auth\IdentificationInterface;
use Viktoriya\PHP2\Http\Auth\JsonBodyUsernameIdentification;
use Viktoriya\PHP2\Http\Auth\AuthenticationInterface;
use Viktoriya\PHP2\Http\Auth\TokenAuthenticationInterface;
use Psr\Log\LoggerInterface;

class CreatePost implements ActionInterface
{
  public function __construct(
    private PostsRepositoryInterface $postsRepository,
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
