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

require_once __DIR__ . '/vendor/autoload.php';

$container = new DIContainer();

$container->bind(
  PDO::class,
  new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
);

$container->bind(
  PostsRepositoryInterface::class,
  SqlitePostsRepository::class
);

$container->bind(
  UsersRepositoryInterface::class,
  SqliteUsersRepository::class
);

$container->bind(
  CommentsRepositoryInterface::class,
  SqliteCommentsRepository::class
);

$container->bind(
  LikeRepositoryInterface::class,
  SqliteLikeRepository::class
);

return $container;
