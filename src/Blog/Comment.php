<?php

namespace Viktoriya\PHP2\Blog;


class Comment
{

  public function __construct(
    private UUID $uuid,
    private Post $post,
    private User $user,
    private string $commentText
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
   * @return Post
   */
  public function getPost(): Post
  {
    return $this->post;
  }

  /**
   * @param Post $post
   */
  public function setPost(Post $post): void
  {
    $this->post = $post;
  }

  /**
   * @return string
   */
  public function getCommentText(): string
  {
    return $this->commentText;
  }

  /**
   * @param string $commentText
   */
  public function setCommentText(string $commentText): void
  {
    $this->commentText = $commentText;
  }

  public function __toString()
  {
    return $this->user . ' опубликовал комментарий: "' . $this->commentText . '"' . PHP_EOL;
  }
}
