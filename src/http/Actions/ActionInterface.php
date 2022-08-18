<?php

namespace LksKndb\Php2\http\Actions;

use LksKndb\Php2\http\Request;
use LksKndb\Php2\http\Response;

interface ActionInterface
{
    public function handle (Request $request) : Response;
}