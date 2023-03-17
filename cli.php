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
use Viktoriya\PHP2\Blog\User;
use Viktoriya\PHP2\Blog\UUID;
use Viktoriya\PHP2\Person\Name;

require_once __DIR__ . '/vendor/autoload.php';

$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');
$userRepository = new SqliteUsersRepository($connection);
$postRepository = new SqlitePostsRepository($connection);
$commentRepository = new SqliteCommentsRepository($connection);


try {
  $user = $userRepository->get(new UUID('8aa1fe08-f293-4b2a-afef-ca9841bf14fb'));
  $post = $postRepository->get(new UUID('865f75b6-91a9-4770-8f9a-d75cf2188978'));
  $comment = $commentRepository->get(new UUID('3437d0f6-8939-4d47-af37-33ae09206570'));

  print_r($comment);
} catch (AppException $e) {
  echo "{$e->getMessage()}\n";
}
