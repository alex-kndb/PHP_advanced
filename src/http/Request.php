<?php

namespace LksKndb\Php2\http;

use LksKndb\Php2\Exceptions\HttpException;
use LksKndb\Php2\Exceptions\JsonException;

class Request
{
    public function __construct(
        private array  $get,
        private array  $server,
        private string $body
    )
    {
    }

    // 1. Метод для получения пути запроса
    // Напрмер, для http://example.com/some/page?x=1&y=acb
    // путём будет строка '/some/page'
    /**
     * @throws HttpException
     */
    public function path(): string
    {
        if (!array_key_exists('REQUEST_URI', $this->server)) {
            throw new HttpException('Cannot get path from the request!');
        }
        // http://127.0.0.1:8000/some/path?some_param=123
        $path = parse_url($this->server['REQUEST_URI'], PHP_URL_PATH);
        // /some/path

        if(!$path){
            throw new HttpException('Cannot get path from the request!');
        }
        return $path;
    }


    // 2. Метод для получения значения
    // определённого параметра строки запроса
    // Напрbмер, для http://example.com/some/page?x=1&y=acb
    // значением параметра x будет строка '1'
    /**
     * @throws HttpException
     */
    public function query(string $param): string
    {
        if(!array_key_exists($param, $this->get)){
            throw new HttpException("No such parameter in the request: $param");
        }
        $value =  trim($this->get[$param]);
        if(empty($value)){
            throw new HttpException("Empty query parameter in the request: $param");
        }
        return $value;
    }


    // 3. Метод для получения значения
    // определённого заголовка
    /**
     * @throws HttpException
     */
    public function header(string $header) : string
    {
        $headerName = mb_strtoupper('http_'.str_replace('-','_', $header));
        if(!array_key_exists($headerName, $this->server)){
            throw new HttpException("No such header in the request: $header");
        }
        $value = trim($this->server[$headerName]);
        if(empty($value)){
            throw new HttpException("Empty query header in the request: $header");
        }
        return $value;
    }


    // 4. пределяем метод запроса
    /**
     * @throws HttpException
     */
    public function method() : string
    {
        if(!array_key_exists('REQUEST_METHOD', $this->server)){
            throw new HttpException("cannot get method from the request!");
        }
        return $this->server['REQUEST_METHOD'];
    }


    // 5. Метод для получения массива, сформированного
    // из json-форматированного тела запроса
    /**
     * @throws HttpException
     * @throws \JsonException
     */
    public function jsonBody() : array
    {
        try{
            $data = json_decode($this->body, associative: true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new HttpException("Cannot decode json body of the request!");
        }
        if(!is_array($data)){
            throw new HttpException("Not an array/object in json body of the request!");
        }
        return $data;
    }


    // 6. Метод для получения отдельного поля
    // из json-форматированного тела запроса
    /**
     * @throws HttpException
     * @throws \JsonException
     */
    public function jsonBodyField(string $field) : mixed
    {
        $data = $this->jsonBody();
        if(!array_key_exists($field, $data)){
            throw new HttpException("No such field in the body of the response: $field");
        }
        if(empty($data[$field])){
            throw new HttpException("Empty field in the body of the request: $field");
        }
        return $data[$field];
    }
}