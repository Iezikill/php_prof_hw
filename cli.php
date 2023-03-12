<?php
//точка входа для запуска через консоль

//псевдоним имени класса
use Viktoriya\PHP2\Blog\User; //use добавит приставку src\Blog\ всем классам User
use Viktoriya\PHP2\Blog\Post; //use добавит приставку src\Blog\ всем классам User
use Viktoriya\PHP2\Person\Name;
use Viktoriya\PHP2\Person\Person;

//composer - аналог npm в JS (исп-ся для скачивания библиотек и для реализации автозагрузки классов)

//функция для автоподключения классов
spl_autoload_register('load'); //регистрируем функцию load; spl_autoload_register автоматически подключает, когда нужно найти какой-то класс
function load($className)
{
  $file = $className . ".php";
  $file = str_replace(["\\", "Viktoriya/PHP2"], ["/", "src"], $file);
  if (file_exists($file)) {
    include $file;
  }
}

$name = new Name('Viktoriya', 'Goncharova');
$user = new User(1, $name, "Admin");
echo $user;

$person = new Person($name, new DateTimeImmutable());

$post = new Post(
  1,
  $person,
  'Всем привет!'
);
echo $post;
