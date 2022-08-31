<?php

namespace LksKndb\Php2\Blog\Commands\Posts;

use LksKndb\Php2\Blog\Repositories\PostsRepositories\PostsRepositoriesInterface;
use LksKndb\Php2\Blog\UUID;
use LksKndb\Php2\Exceptions\Posts\PostNotFoundException;
use LksKndb\Php2\Exceptions\User\InvalidUuidException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class DeletePost extends Command
{
    public function __construct(
        private PostsRepositoriesInterface $postsRepository
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('posts:delete')
            ->setDescription('Deletes a post by uuid')
            ->addArgument(
                'uuid',
                InputArgument::REQUIRED,
                'UUID of a post to delete')
            ->addOption(
                'check-existence',
                'c',
                InputOption::VALUE_NONE,
                'Check if a post actually exists'
            );
    }

    /**
     * @throws InvalidUuidException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $question = new ConfirmationQuestion('Delete post [Y/n]? ', false);

        if (!$this->getHelper('question')
            ->ask($input, $output, $question)
        ) {
            return Command::SUCCESS;
        }

        try{
            $uuid = new UUID($input->getArgument('uuid'));
        } catch (InvalidUuidException $e){
            $output->writeln($e->getMessage());
            return Command::FAILURE;
        }

        if ($input->getOption('check-existence')) {
            try {
                $this->postsRepository->getPostByUUID($uuid);
            } catch (PostNotFoundException $e) {
                $output->writeln($e->getMessage());
                return Command::FAILURE;
            }
        }

        $this->postsRepository->deletePost($uuid);

        $output->writeln("Post $uuid deleted");
        return Command::SUCCESS;
    }
}