<?php

namespace Viktoriya\PHP2\Blog\Commands\Users;

use Viktoriya\PHP2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Viktoriya\PHP2\Blog\User;
use Viktoriya\PHP2\Blog\UUID;
use Viktoriya\PHP2\Person\Name;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class UpdateUser extends Command
{
  public function __construct(
    private UsersRepositoryInterface $usersRepository,
  ) {
    parent::__construct();
  }

  protected function configure(): void
  {
    $this->setName('users:update')
      ->setDescription('Updates a user')
      ->addArgument(
        'uuid',
        InputArgument::REQUIRED,
        'UUID of a user to update'
      )
      ->addOption(
        'first-name',
        'f',
        InputOption::VALUE_OPTIONAL,
        'First name',
      )
      ->addOption(
        'last-name',
        'l',
        InputOption::VALUE_OPTIONAL,
        'Last name',
      );
  }

  protected function execute(
    InputInterface  $input,
    OutputInterface $output,
  ): int {
    $firstName = $input->getOption('first-name');
    $lastName = $input->getOption('last-name');
    if (empty($firstName) && empty($lastName)) {
      $output->writeln('Nothing to update');
      return Command::SUCCESS;
    }
    $uuid = new UUID($input->getArgument('uuid'));
    $user = $this->usersRepository->get($uuid);
    $updatedName = new Name(
      firstName: empty($firstName)
        ? $user->name()->first() : $firstName,
      lastName: empty($lastName)
        ? $user->name()->last() : $lastName,
    );
    $updatedUser = new User(
      uuid: $uuid,
      name: $updatedName,
      username: $user->username(),
      hashedPassword: $user->hashedPassword()
    );

    $this->usersRepository->save($updatedUser);
    $output->writeln("User updated: $uuid");
    return Command::SUCCESS;
  }
}
