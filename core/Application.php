<?php

namespace app\core;

/**
 *
 */
class Application
{


    public static string $ROOT;
    public static Application $app;
    public Router $router;
    public Request $request;
    public Response $response;
    public Database $db;


    /**
     * @param string $ROOT
     */
    public function __construct(string $ROOT){
        self::$ROOT = $ROOT;
        self::$app = $this;
        $this->response = new Response();
        $this->request = new Request();
        $this->router = new Router($this->request, $this->response);

    }

    /**
     * @return void
     */
    public function run(){
        echo $this->router->resolve();
    }

}
