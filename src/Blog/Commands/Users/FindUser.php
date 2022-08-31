<?php

namespace LksKndb\Php2\Blog\Commands\Users;

use LksKndb\Php2\Blog\Repositories\UsersRepositories\UsersRepositoriesInterface;
use LksKndb\Php2\Exceptions\User\UserNotFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FindUser extends Command
{
    public function __construct(
        private UsersRepositoriesInterface $usersRepository
    ) {
        parent::__construct();
    }

    protected function configure() : void
    {
        $this
            ->setName('users:find')
            ->setDescription('Finds user by username')
            ->addArgument('username', InputArgument::REQUIRED, 'Username');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $output->writeln('Find user command started');

        $username = $input->getArgument('username');

        try{
            $user = $this->usersRepository->getUserByUsername($username);
        } catch (UserNotFoundException $e){
            $output->writeln($e->getMessage());
            return Command::FAILURE;
        }

        $output->writeln("User found:\n{$user->getName()}");

        return Command::SUCCESS;
    }
}