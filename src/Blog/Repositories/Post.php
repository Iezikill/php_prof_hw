<?php

namespace Viktoriya\PHP2\Blog\Repositories;

class Post
{
  private int $id;
  private User $user;
  private string $title;
  private string $text;

  /**
   * @param User $user
   * @param string $text
   * @param string $title
   * @param int $id
   */
  public function __construct(int $id, User $user, string $title, string $text)
  {
    $this->id = $id;
    $this->text = $text;
    $this->title = $title;
    $this->user = $user;
  }

  public function __toString()
  {
    return $this->user . ' опубликовал статью "' . $this->text . '" под названием: "' . $this->title . '"' . PHP_EOL;
  }
}
