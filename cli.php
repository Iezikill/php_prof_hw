<?php

use Viktoriya\PHP2\Blog\Commands\Arguments;
use Viktoriya\PHP2\Blog\Commands\CreateUserCommand;
use Viktoriya\PHP2\Blog\Comment;
use Viktoriya\PHP2\Blog\Exceptions\AppException;
use Viktoriya\PHP2\Blog\Exceptions\CommandException;
use Viktoriya\PHP2\Blog\Post;
use Viktoriya\PHP2\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use Viktoriya\PHP2\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Viktoriya\PHP2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Psr\Log\LoggerInterface;

$container = require __DIR__ . '/bootstrap.php';

$logger = $container->get(LoggerInterface::class);

try {
  $command = $container->get(CreateUserCommand::class);
  $command->handle(Arguments::fromArgv($argv));
} catch (Exception $e) {
  $logger->error($e->getMessage(), ['exception' => $e]);
  echo $e->getMessage();
}
