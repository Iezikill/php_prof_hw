<?php

namespace Viktoriya\PHP2\Blog\Repositories\CommentsRepository;

use Viktoriya\PHP2\Blog\Comment;
use Viktoriya\PHP2\Blog\UUID;

interface CommentsRepositoryInterface
{
  public function save(Comment $comment): void;
  public function get(UUID $uuid): Comment;
  public function delete(UUID $uuid): void;
}
