<?php

use Viktoriya\PHP2\Blog\Container\DIContainer;
use Viktoriya\PHP2\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use Viktoriya\PHP2\Blog\Repositories\AuthTokensRepository\SqliteAuthTokensRepository;
use Viktoriya\PHP2\Blog\Repositories\LikeRepository\LikeRepositoryInterface;
use Viktoriya\PHP2\Blog\Repositories\LikeRepository\SqliteLikeRepository;
use Viktoriya\PHP2\Blog\Repositories\PostsRepository\PostRepositoryInterface;
use Viktoriya\PHP2\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Viktoriya\PHP2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Viktoriya\PHP2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Viktoriya\PHP2\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use Viktoriya\PHP2\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use Viktoriya\PHP2\Http\Auth\AuthenticationInterface;
use Viktoriya\PHP2\Http\Auth\BearerTokenAuthentication;
use Viktoriya\PHP2\Http\Auth\IdentificationInterface;
use Viktoriya\PHP2\Http\Auth\JsonBodyUsernameIdentification;
use Viktoriya\PHP2\Http\Auth\JsonBodyUuidIdentification;
use Viktoriya\PHP2\Http\Auth\PasswordAuthentication;
use Viktoriya\PHP2\Http\Auth\PasswordAuthenticationInterface;
use Viktoriya\PHP2\Http\Auth\TokenAuthenticationInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Dotenv\Dotenv;
use Faker\Generator;
use Faker\Provider\Lorem;
use Faker\Provider\ru_RU\Internet;
use Faker\Provider\ru_RU\Person;
use Faker\Provider\ru_RU\Text;

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

$faker = new Generator();
$faker->addProvider(new Person($faker));
$faker->addProvider(new Text($faker));
$faker->addProvider(new Internet($faker));
$faker->addProvider(new Lorem($faker));

$container->bind(
  Generator::class,
  $faker
);

$container->bind(
  TokenAuthenticationInterface::class,
  BearerTokenAuthentication::class
);

$container->bind(
  PasswordAuthenticationInterface::class,
  PasswordAuthentication::class
);

$container->bind(
  AuthTokensRepositoryInterface::class,
  SqliteAuthTokensRepository::class
);

$container->bind(
  AuthenticationInterface::class,
  PasswordAuthentication::class
);

$container->bind(
  IdentificationInterface::class,
  JsonBodyUsernameIdentification::class
);

$container->bind(
  LoggerInterface::class,
  $logger
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
