<?php
//Добрый вечер, Александр.
//Сдаю вам невыполненную работу, т.к. крайний срок сдачи дз подходит к концу
//Я постараюсь успеть доделать дз до вашей проверки
//Если не успела, поставьте пжл "не сдано", чтоб время продлилось и я успела доделать
//Очень надеюсь на Ваше понимание =) на работе колапс...




//псевдоним имени класса
use Geekbrains\Blog\User; //use добавит приставку Geekbrains\Blog\ всем классам User
use Person\{Name, Person};


//точка входа для запуска через консоль

// function __autoload($className) //автозагрузка классов, некорректно применять т.к не будет работать с папками и не работает в php8
// {
//   $file = $className . ".php";
//   if (file_exists($file)) {
//     include $file;
//   }
// }

//лучше использовать spl_autoload_register() для автоподключения классов
spl_autoload_register('load'); //регистрируем функцию load; spl_autoload_register автоматически подключает, когда нужно найти какой-то класс
function load($className)
{
  $file = $className . ".php";
  $file = str_replace("\\", "/", $file);
  if (file_exists($file)) {
    include $file;
  }
}

$name = new Name('Viktoriya', 'Goncharova');
$user = new User(1, $name, "Admin");
echo $user;


$person = new Person($name, new DateTimeImmutable());

echo $person;
