<?php

namespace Viktoriya\PHP2\Blog;

class Post
{
  public function __construct(
    private UUID $uuid,
    private User $user,
    private string $title,
    private string $text,
  ) {
  }

  /**
   * @return UUID
   */
  public function getUuid(): UUID
  {
    return $this->uuid;
  }

  /**
   * @param UUID $uuid
   */
  public function setUuid(UUID $uuid): void
  {
    $this->uuid = $uuid;
  }

  /**
   * @return User
   */
  public function getUser(): User
  {
    return $this->user;
  }

  /**
   * @param User $user
   */
  public function setUser(User $user): void
  {
    $this->user = $user;
  }

  /**
   * @return string
   */
  public function getTitle(): string
  {
    return $this->title;
  }

  /**
   * @param string $title
   */
  public function setTitle(string $title): void
  {
    $this->title = $title;
  }

  /**
   * @return string
   */
  public function getText(): string
  {
    return $this->text;
  }

  /**
   * @param string $text
   */
  public function setText(string $text): void
  {
    $this->text = $text;
  }

  public function __toString()
  {
    return $this->user . ' опубликовал статью "' . $this->text . '" под названием: "' . $this->title . '"' . PHP_EOL;
  }
}
