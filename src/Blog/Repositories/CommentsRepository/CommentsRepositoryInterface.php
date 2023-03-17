<?php

namespace Viktoriya\PHP2\Blog\Repositories\CommentsRepository;

use Viktoriya\PHP2\Blog\Comment;
use Viktoriya\PHP2\Blog\UUID;

interface CommentsRepositoryInterface
{
  public function save(Comment $user): void;
  public function get(UUID $uuid): Comment;
}
