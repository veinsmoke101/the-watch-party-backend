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
        if(file_exists(dirname(__DIR__).$model)){
            $model = "\app\models\\".$model;
            return new $model();
        }
        return null;
    }

}