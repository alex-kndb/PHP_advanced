<?php

namespace LksKndb\Php2\Blog\Commands\Users;

use LksKndb\Php2\Blog\Name;
use LksKndb\Php2\Blog\Repositories\UsersRepositories\UsersRepositoriesInterface;
use LksKndb\Php2\Blog\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUser extends Command
{
    public function __construct(
        private UsersRepositoriesInterface $usersRepository
    ) {
        parent::__construct();
    }

    protected function configure() : void
    {
        $this
            ->setName('users:create')
            ->setDescription('Creates new user')
            ->addArgument('first_name', InputArgument::REQUIRED, 'First name')
            ->addArgument('last_name', InputArgument::REQUIRED, 'Last name')
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('password', InputArgument::REQUIRED, 'Password');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $output->writeln('Create user command started');

        $username = $input->getArgument('username');
        if($this->usersRepository->isUserExists($username)){
            $output->writeln("User already exists: $username");
            return Command::FAILURE;
        }

        $user = User::createFrom(
            new Name(
                $input->getArgument('first_name'),
                $input->getArgument('last_name'),
                $input->getArgument('username')
            ),
            $input->hasArgument('password')
        );

        $this->usersRepository->saveUser($user);

        $output->writeln('User created: ' . $user->getUUID());

        return Command::SUCCESS;
    }
}