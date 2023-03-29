<?php

namespace Viktoriya\PHP2\Blog;

use Viktoriya\PHP2\Person\Name;

class User
{
  private UUID $uuid;
  private Name $name;
  private string $username;

  /**
   * @param UUID $uuid
   * @param Name $name
   * @param string $username
   */

  public function __construct(UUID $uuid, Name $name, string $username)
  {
    $this->uuid = $uuid;
    $this->name = $name;
    $this->username = $username;
  }

  public function uuid(): UUID
  {
    return $this->uuid;
  }

  public function name(): Name
  {
    return $this->name;
  }

  public function setName(Name $name): void
  {
    $this->name = $name;
  }

  public function username(): string
  {
    return $this->username;
  }

  public function setUsername(string $username): void
  {
    $this->username = $username;
  }

  public function __toString(): string
  {
    return "Юзер $this->uuid с именем $this->name и логином $this->username";
  }
}
