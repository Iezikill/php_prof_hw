<?php

use Viktoriya\PHP2\Blog\Exceptions\AppException;
use Viktoriya\PHP2\Http\Actions\Comments\CreateComment;
use Viktoriya\PHP2\Http\Actions\Comments\DeleteComment;
use Viktoriya\PHP2\Http\Actions\Posts\CreatePost;
use Viktoriya\PHP2\Http\Actions\Posts\DeletePost;
use Viktoriya\PHP2\Http\Actions\Users\CreateUser;
use Viktoriya\PHP2\Http\Actions\Users\FindByUsername;
use Viktoriya\PHP2\Http\ErrorResponse;
use Viktoriya\PHP2\Http\Request;
use Viktoriya\PHP2\Http\Actions\Likes\CreateLike;
use Viktoriya\PHP2\Http\Actions\Likes\ShowLikes;

use Viktoriya\PHP2\Blog\Exceptions\HttpException;
use Psr\Log\LoggerInterface;

// require_once __DIR__ . '/vendor/autoload.php';
$container = require __DIR__ . '/bootstrap.php';
$logger = $container->get(LoggerInterface::class);

$request = new Request(
  $_GET,
  $_SERVER,
  file_get_contents('php://input'),
);

$routes = [
  'GET' => [
    '/users/show' => FindByUsername::class,
    '/likes/show' => ShowLikes::class,
  ],
  'POST' => [
    '/users/create' => CreateUser::class,
    '/posts/create' => CreatePost::class,
    '/comments/create' => CreateComment::class,
    '/likes/create' => CreateLike::class,
  ],
  'DELETE' => [
    '/posts' => DeletePost::class,
    '/comments' => DeleteComment::class,
  ],
];

try {
  $path = $request->path();
} catch (HttpException $e) {
  (new ErrorResponse)->send();
  $logger->warning($e->getMessage());
  return;
}

try {
  $method = $request->method();
} catch (HttpException $e) {
  $logger->warning($e->getMessage());
  (new ErrorResponse)->send();
  return;
}

if (!array_key_exists($method, $routes) || !array_key_exists($path, $routes[$method])) {
  $message = "Route not found: $method $path";
  $logger->notice($message);
  (new ErrorResponse($message))->send();
  return;
}

$actionClassName = $routes[$method][$path];

$action = $container->get($actionClassName);

try {
  $response = $action->handle($request);
  $response->send();
} catch (AppException $e) {
  $logger->error($e->getMessage(), ['exception' => $e]);
  (new ErrorResponse($e->getMessage()))->send();
}
$response->send();
