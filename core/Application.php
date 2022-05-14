<?php

namespace app\core;

use Pusher\Pusher;
use Pusher\PusherException;

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
    public Pusher $pusher;


    /**
     * @param string $ROOT
     */
    public function __construct(string $ROOT){
        self::$ROOT = $ROOT;
        self::$app = $this;
        $this->response = new Response();
        $this->request = new Request();
        $this->router = new Router($this->request, $this->response);

        // pusher
        $options = array(
            'cluster' => 'eu',
            'useTLS' => true
        );
        try {
            $this->pusher = new Pusher(
                'ee67aad443c2735b4c8f',
                'c8f1ee535c4d519ccfaf',
                '1387861',
                $options
            );
        }catch(PusherException $pusherException){
            echo 'Pusher Exception : ' . $pusherException;
        }
    }

    /**
     * @return void
     */
    public function run(){
        echo $this->router->resolve();
    }

}
