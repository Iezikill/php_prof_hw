<?php

namespace Viktoriya\PHP2\Blog\Repositories;

class Comment
{
  private int $id;
  private User $user;
  private Post $post;
  private string $commentText;

  /**
   * @param int $id
   * @param User $user
   * @param Post $post
   * @param string $commentText
   */
  public function __construct(int $id, User $user, Post $post, string $commentText)
  {
    $this->id = $id;
    $this->user = $user;
    $this->post = $post;
    $this->commentText = $commentText;
  }


  /**
   * @return int
   */
  public function getId(): int
  {
    return $this->id;
  }

  /**
   * @param int $id
   */
  public function setId(int $id): void
  {
    $this->id = $id;
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
    return $this->user . ' опубликовал комментарий: "' . $this->commentText . PHP_EOL;
  }
}
