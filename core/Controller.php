<?php

namespace app\core;


class Controller
{

    protected Response $response;


    public function __construct()
    {
        $this->response = new Response();
        $this->cors();
    }


    protected function cors()
    {
        // Function that handles  CORS
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');
        }

        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) // may also be using PUT, PATCH, HEAD etc
            {
                header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
            }

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            }

            exit(0);
        }
    }


    /**
     * @param $view
     * @param array $params
     * @return string
     */
    public function render($view, array $params = []): string
    {
        return Application::$app->router->renderView($view, $params);
    }

    /**
     * @param $model
     * @return mixed|null
     */

    public function model($model)
    {

        if(file_exists(dirname(__DIR__)."\models\\".$model.".php")){
            $model = "\app\models\\".$model;
            return new $model();
        }
        return null;
    }
}