<?php

namespace app\core;

class Request
{
    public function getPath(): string
    {
        return parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
    }

    public function getMethod(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
        
    }
}