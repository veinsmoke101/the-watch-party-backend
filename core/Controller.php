<?php

namespace app\core;


class Controller
{


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
//        var_dump(dirname(__DIR__)."\models\\".$model.".php");
//        die();
        if(file_exists(dirname(__DIR__)."\models\\".$model.".php")){
            $model = "\app\models\\".$model;
            return new $model();
        }
        return null;
    }
}