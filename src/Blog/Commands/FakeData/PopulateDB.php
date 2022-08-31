<?php

namespace LksKndb\Php2\Blog\Commands\FakeData;

use Faker\Generator;
use LksKndb\Php2\Blog\Comment;
use LksKndb\Php2\Blog\Name;
use LksKndb\Php2\Blog\Post;
use LksKndb\Php2\Blog\Repositories\CommentsRepositories\CommentsRepositoriesInterface;
use LksKndb\Php2\Blog\Repositories\PostsRepositories\PostsRepositoriesInterface;
use LksKndb\Php2\Blog\Repositories\UsersRepositories\UsersRepositoriesInterface;
use LksKndb\Php2\Blog\User;
use LksKndb\Php2\Blog\UUID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateDB extends Command
{
    public function __construct(
        private Generator $faker,
        private UsersRepositoriesInterface $usersRepository,
        private PostsRepositoriesInterface $postsRepository,
        private CommentsRepositoriesInterface $commentsRepository
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('fake-data:populate-db')
            ->setDescription('Populates DB with fake data')
            ->addOption('users-number', 'u', InputOption::VALUE_OPTIONAL, 'Number of faked users')
            ->addOption('posts-number', 'p', InputOption::VALUE_OPTIONAL, 'Number of faked posts')
            ->addOption('comments-number', 'c', InputOption::VALUE_OPTIONAL, 'Number of faked comments');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $usersNumber = $input->getOption('users-number');
        $postsNumber = $input->getOption('posts-number');
        $commentsNumber = $input->getOption('comments-number');

        if (empty($usersNumber) && empty($postsNumber) && empty($commentsNumber)) {
            $output->writeln('Default values applied');
            $usersNumber = 3;
            $postsNumber = 3;
            $commentsNumber = 3;
        }

        for ($i = 0; $i < $usersNumber; $i++) {
            $user = $this->createFakeUser();
            $output->writeln('User created: ' . $user->getName()->getUsername());
            for ($j = 0; $j < $postsNumber; $j++) {
                $post = $this->createFakePost($user);
                $output->writeln('Post created: ' . $post->getPost());
                for ($k = 0; $k < $commentsNumber; $k++) {
                    $comment = $this->createFakeComment($user, $post);
                    $output->writeln('Comment created: ' . $comment->getUuid());
                }
            }
        }

        return Command::SUCCESS;
    }

    private function createFakeUser(): User
    {
        $user = User::createFrom(
            new Name(
                $this->faker->firstName,
                $this->faker->lastName,
                $this->faker->userName
            ),
            $this->faker->password,
        );
        $this->usersRepository->saveUser($user);
        return $user;
    }

    private function createFakePost(User $author): Post
    {
        $post = new Post(
            UUID::createUUID(),
            $author,
            $this->faker->sentence(6, true),
            $this->faker->realText
        );

        $this->postsRepository->savePost($post);
        return $post;
    }

    private function createFakeComment(User $author, Post $post): Comment
    {
        $post = new Comment(
            UUID::createUUID(),
            $post,
            $author,
            $this->faker->realText
        );

        $this->commentsRepository->saveComment($post);
        return $post;
    }
}