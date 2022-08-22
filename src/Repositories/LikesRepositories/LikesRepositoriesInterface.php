<?php

namespace LksKndb\Php2\Repositories\LikesRepositories;

use LksKndb\Php2\Classes\Like;
use LksKndb\Php2\Classes\UUID;

interface LikesRepositoriesInterface
{
    public function saveLike(Like $like): void;
    public function getLikeByUUID(UUID $uuid): Like;
}