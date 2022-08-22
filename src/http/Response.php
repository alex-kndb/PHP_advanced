<?php

namespace LksKndb\Php2\http;

abstract class Response
{
    protected const SUCCESS = true;

    public function send() : void
    {
        $response = ['success' => static::SUCCESS] + $this->payload();
        header('Content-Type: application/json');
        echo json_encode($response, JSON_THROW_ON_ERROR);
    }

    abstract protected function payload() : array;
}