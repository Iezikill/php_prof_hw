<?php

namespace Viktoriya\PHP2\Blog;

use Viktoriya\PHP2\Person\Person;

class Post
{
  private int $id;
  private Person $author;
  private string $text;

  public function __construct(int $id, Person $author, string $text)
  {
    $this->id = $id;
    $this->text = $text;
    $this->author = $author;
  }

  public function __toString()
  {
    return $this->author . ' пишет: ' . $this->text . PHP_EOL;
  }
}
