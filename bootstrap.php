<?php

use Viktoriya\PHP2\Blog\Container\DIContainer;
use Viktoriya\PHP2\Blog\Repositories\LikeRepository\LikeRepositoryInterface;
use Viktoriya\PHP2\Blog\Repositories\LikeRepository\SqliteLikeRepository;
use Viktoriya\PHP2\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Viktoriya\PHP2\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Viktoriya\PHP2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Viktoriya\PHP2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Viktoriya\PHP2\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use Viktoriya\PHP2\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;

use Viktoriya\PHP2\Http\Auth\IdentificationInterface;
use Viktoriya\PHP2\Http\Auth\JsonBodyUsernameIdentification;
use Viktoriya\PHP2\Http\Auth\JsonBodyUuidIdentification;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';

$container = new DIContainer();

Dotenv::createImmutable(__DIR__)->safeLoad();

$container->bind(
  PDO::class,
  new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
);

$logger = (new Logger('blog'));

if ('yes' === $_ENV['LOG_TO_FILES']) {
  $logger
    ->pushHandler(new StreamHandler(
      __DIR__ . '/logs/blog.log'
    ))
    ->pushHandler(new StreamHandler(
      __DIR__ . '/logs/blog.error.log',
      level: Logger::ERROR,
      bubble: false,
    ));
}

if ('yes' === $_ENV['LOG_TO_CONSOLE']) {
  $logger->pushHandler(
    new StreamHandler("php://stdout")
  );
}

$container->bind(
  IdentificationInterface::class,
  JsonBodyUsernameIdentification::class
);

$container->bind(
  LoggerInterface::class,
  $logger
);

$container->bind(
  PostRepositoryInterface::class,
  SqlitePostRepository::class
);

$container->bind(
  UsersRepositoryInterface::class,
  SqliteUsersRepository::class
);

$container->bind(
  CommentRepositoryInterface::class,
  SqliteCommentRepository::class
);

$container->bind(
  LikeRepositoryInterface::class,
  SqliteLikeRepository::class
);

return $container;
