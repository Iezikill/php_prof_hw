<?php

namespace Viktoriya\PHP2\Blog;

use Viktoriya\PHP2\Person\Name;

class User
{
  private UUID $uuid;
  private Name $name;
  private string $username;
  private string $hashedPassword;

  /**
   * @param UUID $uuid
   * @param Name $name
   * @param string $username
   */

  public function __construct(UUID $uuid, Name $name, string $username, string $hashedPassword)
  {
    $this->uuid = $uuid;
    $this->name = $name;
    $this->username = $username;
    $this->hashedPassword =  $hashedPassword;
  }

  public function hashedPassword(): string
  {
    return $this->hashedPassword;
  }

  private static function hash(string $password, UUID $uuid): string
  {
    return hash('sha256',  $uuid . $password);
  }

  public function checkPassword(string $password): bool
  {
    return $this->hashedPassword === self::hash($password, $this->uuid);
  }

  public static function createFrom(
    string $username,
    string $password,
    Name   $name
  ): self {
    $uuid = UUID::random();
    return new self(
      $uuid,
      $name,
      $username,
      self::hash($password, $uuid),
    );
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
