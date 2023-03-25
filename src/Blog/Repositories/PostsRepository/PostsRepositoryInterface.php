<?php

namespace Viktoriya\PHP2\Blog\Repositories\PostsRepository;

use Viktoriya\PHP2\Blog\Post;
use Viktoriya\PHP2\Blog\UUID;

interface PostsRepositoryInterface
{
  public function save(Post $post): void;
  public function get(UUID $uuid): Post;
  public function delete(UUID $uuid): void;
}
