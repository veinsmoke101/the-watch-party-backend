<?php

namespace app\core;

class Request
{

    private array $routeParams = [];

    public function getPath(): string
    {
        return parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
    }

    public function getMethod(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
        
    }

    public function setRouteParams($params): Request
    {
        $this->routeParams = $params;
        return $this;
    }

    public function getRouteParams(): array
    {

        return $this->routeParams;
    }
}