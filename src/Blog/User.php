<?php

namespace Viktoriya\PHP2\Blog; //слева виртуальное пространство, справа реальный путь
//Viktoriya - имя создателя
//PHP2 - имя проекта

use Viktoriya\PHP2\Person\Name;

class User
{
  private int $id;
  private Name $username;
  private string $login;

  /**
   * @param int $id;
   * @param Name $username;
   * @param string $login;
   */
  public function __construct(int $id, Name $username, string $login)
  {
    $this->id = $id;
    $this->username = $username;
    $this->login = $login;
  }

  public function __toString(): string //метод для получения объекта в виде строки
  {
    return "Юзер $this->id с именем $this->username и логином $this->login." . PHP_EOL;
  }
}
