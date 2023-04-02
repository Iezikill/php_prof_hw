<?php

namespace Viktoriya\PHP2\Blog\Commands\Users;

use Viktoriya\PHP2\Blog\Exceptions\UserNotFoundException;
use Viktoriya\PHP2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Viktoriya\PHP2\Blog\User;
use Viktoriya\PHP2\Person\Name;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class CreateUser extends Command
{
  public function __construct(
    private UsersRepositoryInterface $usersRepository,
  ) {
    parent::__construct();
  }

  protected function configure(): void
  {
    $this->setName('users:create')
      ->setDescription('Creates new user')
      ->addArgument(
        'first_name',
        InputArgument::REQUIRED,
        'First name'
      )
      ->addArgument('last_name', InputArgument::REQUIRED, 'Last name')
      ->addArgument('username', InputArgument::REQUIRED, 'Username')
      ->addArgument('password', InputArgument::REQUIRED, 'Password');
  }

  protected function execute(
    InputInterface  $input,
    OutputInterface $output,
  ): int {
    $output->writeln('Create user command started');
    $username = $input->getArgument('username');
    if ($this->userExists($username)) {
      $output->writeln("User already exists: $username");
      return Command::FAILURE;
    }

    $user = User::createFrom(
      $username,
      $input->getArgument('password'),
      new Name(
        $input->getArgument('first_name'),
        $input->getArgument('last_name')
      ),
    );

    $this->usersRepository->save($user);
    $output->writeln('User created: ' . $user->uuid());
    return Command::SUCCESS;
  }

  private function userExists(string $username): bool
  {
    try {
      $this->usersRepository->getByUsername($username);
    } catch (UserNotFoundException) {
      return false;
    }
    return true;
  }
}
