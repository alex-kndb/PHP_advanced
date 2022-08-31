<?php

namespace LksKndb\Php2\Blog\Commands\Users;

use LksKndb\Php2\Blog\Name;
use LksKndb\Php2\Blog\Repositories\UsersRepositories\UsersRepositoriesInterface;
use LksKndb\Php2\Blog\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateUser extends Command
{
    public function __construct(
        private UsersRepositoriesInterface $usersRepository
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('users:update')
            ->setDescription('Update info about existing user')
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addOption('first-name', 'f', InputOption::VALUE_OPTIONAL, 'First name')
            ->addOption('last-name', 'l', InputOption::VALUE_OPTIONAL, 'Last name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $firstName = $input->getOption('first-name');
        $lastName = $input->getOption('last-name');

        if (empty($firstName) && empty($lastName)) {
            $output->writeln('Nothing to update');
            return Command::SUCCESS;
        }

        $username = $input->getArgument('username');

        if(!$this->usersRepository->isUserExists($username)){
            $output->writeln("User not exists: $username");
            return Command::FAILURE;
        }

        $user = $this->usersRepository->getUserByUsername($username);

        $updatedUser = new User(
            uuid: $user->getUUID(),
            name: new Name(
                firstName: empty($firstName) ? $user->getName()->getFirstName() : $firstName,
                lastName: empty($lastName) ? $user->getName()->getLastName() : $lastName,
                username: $username
            ),
            hashedPassword: $user->hashedPassword(),
            registeredOn: $user->getRegisteredOn()
        );

        $this->usersRepository->saveUser($updatedUser);

        $output->writeln('User updated: ' . $user->getName()->getUsername());
        return Command::SUCCESS;


    }
}