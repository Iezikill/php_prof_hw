<?php

namespace Viktoriya\PHP2\Blog\Repositories\LikeRepository;

use Viktoriya\PHP2\Blog\UUID;
use Viktoriya\PHP2\Blog\Like;

interface LikeRepositoryInterface
{
  public function save(Like $like): void;
  public function getByPostUuid(UUID $uuid): array;
}
