<?php

namespace Viktoriya\PHP2\Blog\Commands\FakeData;

use Viktoriya\PHP2\Blog\Comment;
use Viktoriya\PHP2\Blog\Post;
use Viktoriya\PHP2\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use Viktoriya\PHP2\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Viktoriya\PHP2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Viktoriya\PHP2\Blog\User;
use Viktoriya\PHP2\Blog\UUID;
use Viktoriya\PHP2\Person\Name;
use Symfony\Component\Console\Command\Command;
use Faker\Generator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class PopulateDB extends Command
{
  public function __construct(
    private Generator $faker,
    private UsersRepositoryInterface $usersRepository,
    private PostsRepositoryInterface $postsRepository,
    private CommentsRepositoryInterface $commentsRepository,
  ) {
    parent::__construct();
  }

  protected function configure(): void
  {
    $this
      ->setName('fake-data:populate-db')
      ->setDescription('Populates DB with fake data')
      ->addOption(
        'users-quantity',
        'u',
        InputOption::VALUE_OPTIONAL,
        'Users quantity'
      )
      ->addOption(
        'posts-quantity',
        'p',
        InputOption::VALUE_OPTIONAL,
        'Posts quantity'
      )
      ->addOption(
        'comments-quantity',
        'c',
        InputOption::VALUE_OPTIONAL,
        'Comments quantity'
      );
  }

  protected function execute(
    InputInterface $input,
    OutputInterface $output,
  ): int {
    $usersQuantity = $input->getOption('users-quantity');
    $postsQuantity = $input->getOption('posts-quantity');
    $commentsQuantity = $input->getOption('comments-quantity');
    $users = [];
    for ($i = 0; $i < $usersQuantity; $i++) {
      $user = $this->createFakeUser();
      $users[] = $user;
      $output->writeln('User created: ' . $user->username());
    }
    foreach ($users as $user) {
      for ($i = 0; $i < $postsQuantity; $i++) {
        $post = $this->createFakePost($user);
        $posts[] = $post;
        $output->writeln('Post created: ' . $post->getTitle());
      }
    }
    foreach ($posts as $post) {
      for ($i = 0; $i < $commentsQuantity; $i++) {
        $comment = $this->createFakeComment($post, $user);
        $output->writeln('Comment created: ' . $comment->getText());
      }
    }
    return Command::SUCCESS;
  }

  private function createFakeUser(): User
  {
    $user = User::createFrom(
      $this->faker->userName,
      $this->faker->password,
      new Name(
        $this->faker->firstName,
        $this->faker->lastName
      )
    );
    $this->usersRepository->save($user);
    return $user;
  }

  private function createFakePost(User $author): Post
  {
    $post = new Post(
      UUID::random(),
      $author,
      $this->faker->sentence(6, true),
      $this->faker->realText
    );
    $this->postsRepository->save($post);
    return $post;
  }

  private function createFakeComment(Post $post, User $author): Comment
  {
    $comment = new Comment(
      UUID::random(),
      $author,
      $post,
      $this->faker->realText
    );
    $this->commentsRepository->save($comment);
    return $comment;
  }
}
