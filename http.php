<?php

use Viktoriya\PHP2\Blog\Exceptions\AppException;
use Viktoriya\PHP2\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use Viktoriya\PHP2\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Viktoriya\PHP2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Viktoriya\PHP2\Http\Actions\Comments\CreateComment;
use Viktoriya\PHP2\Http\Actions\Comments\DeleteComment;
use Viktoriya\PHP2\Http\Actions\Posts\CreatePost;
use Viktoriya\PHP2\Http\Actions\Posts\DeletePost;
use Viktoriya\PHP2\Http\Actions\Users\CreateUser;
use Viktoriya\PHP2\Http\Actions\Users\FindByUsername;
use Viktoriya\PHP2\Http\ErrorResponse;
use Viktoriya\PHP2\Http\Request;
use Viktoriya\PHP2\Http\SuccessfulResponse;

require_once __DIR__ . '/vendor/autoload.php';

$request = new Request(
  $_GET,
  $_SERVER,
  file_get_contents('php://input'),
);

$routes = [
  'GET' => [
    '/users/show' => new FindByUsername(
      new SqliteUsersRepository(
        new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
      )
    ),

  ],
  'POST' => [
    '/users/create' => new CreateUser(
      new SqliteUsersRepository(
        new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
      )
    ),
    '/posts/create' => new CreatePost(
      new SqliteUsersRepository(
        new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
      ),
      new SqlitePostsRepository(
        new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
      )
    ),
    '/comments/create' => new CreateComment(
      new SqliteUsersRepository(
        new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
      ),
      new SqlitePostsRepository(
        new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
      ),
      new SqliteCommentsRepository(
        new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
      )
    )
  ],
  'DELETE' => [
    '/posts' => new DeletePost(
      new SqlitePostsRepository(
        new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
      )
    ),
    '/comments' => new DeleteComment(
      new SqliteCommentsRepository(
        new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
      )
    )
  ],
];

try {
  $path = $request->path();
} catch (HttpException) {
  (new ErrorResponse)->send();
  return;
}

try {
  $method = $request->method();
} catch (HttpException) {
  (new ErrorResponse)->send();
  return;
}

if (!array_key_exists($method, $routes)) {
  (new ErrorResponse('Not found'))->send();
  return;
}

if (!array_key_exists($path, $routes[$method])) {
  (new ErrorResponse('Not found'))->send();
  return;
}

$action = $routes[$method][$path];
try {
  $response = $action->handle($request);
  $response->send();
} catch (Exception $e) {
  (new ErrorResponse($e->getMessage()))->send();
}
