<?php

use Viktoriya\PHP2\Blog\Post;
use Viktoriya\PHP2\Blog\Comment;
use Viktoriya\PHP2\Blog\User;
use Viktoriya\PHP2\Person\Name;

function load($classname)
{
  $file = $classname . ".php";
  $file = str_replace(["\\", "Viktoriya/PHP2"], ["/", "src"], $file);
  if (file_exists($file)) {
    include $file;
  }
}

include __DIR__ . "/vendor/autoload.php";

$faker = Faker\Factory::create('ru_RU');

$name = new Name(
  $faker->firstName(),
  $faker->lastName()
);
$user = new User(
  $faker->randomDigitNotNull(),
  $name,
  $faker->word(1)
);

$route = $argv[1] ?? null;

switch ($argv[1]) {
  case "user":
    echo $user;
    break;
  case "post":
    $post = new Post(
      $faker->randomDigitNotNull(),
      $user,
      $faker->realText(rand(10, 15)),
      $faker->realText(rand(50, 100))
    );
    echo $post;
    break;
  case "comment":
    $post = new Post(
      $faker->randomDigitNotNull(),
      $user,
      $faker->realText(rand(10, 15)),
      $faker->realText(rand(50, 100))
    );
    $comment = new Comment(
      $faker->randomDigitNotNull(),
      $user,
      $post,
      $faker->realText(rand(50, 100))
    );
    echo $comment;
    break;
  default:
    echo 'Error! Try user, post or comment as arguments for success';
}
