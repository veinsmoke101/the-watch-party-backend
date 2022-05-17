<?php
namespace app\core;

class Router
{
    public array $routes = [];
    private Request $request;
    private Response $response;

    /*
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }


    public function get($path, $callback){
        $this->routes['get'][$path] = $callback;
    }

    public function post($path, $callback){
        $this->routes['post'][$path] = $callback;
    }

    public function put($path, $callback){
        $this->routes['put'][$path] = $callback;
    }

    public function delete($path, $callback){
        $this->routes['delete'][$path] = $callback;
    }

    public function patch($path, $callback){
        $this->routes['patch'][$path] = $callback;
    }

    public function getCallback()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $url = trim($path, '/');

        $routesByMethod = $this->routes[$method] ?? [];
        foreach ($routesByMethod as $route => $callback){
            $route = trim($route, '/');

            if(!$route){
                continue;
            }

            $routeNames = [];

            if(preg_match_all('/\{(\w+)(:[^}]+)?}/', $route, $matches)){
                $routeNames = $matches[1];
            }


            $routeRegex = '@^' .  preg_replace_callback('/\{\w+(:([^}]+))?}/', fn($m) => isset($m[2] ) ? "($m[2])" : '(\w+)', $route) . '$@';


            if (preg_match_all($routeRegex, $url, $valueMatches)){
                $values=[];
                for ($i=1; $i < count($valueMatches); $i++){
                    $values[] = $valueMatches [$i][0];
                }
                $routeParams = array_combine($routeNames, $values);
                $this->request->setRouteParams ($routeParams);
                return $callback;
            }
        }
        return false;
    }

    public function resolve()
    {

        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = $this->routes[$method][$path] ?? false;
        if(!$callback){
            $callback = $this->getCallback();
            if(!$callback){
                $this->response->setStatusCode(404);
                return $this->renderView('_404');
            }
        }

        if(is_string($callback)){
            return $this->renderView($callback);
        }

        return call_user_func($callback);
    }


    public function renderView(string $view, array $params = [])
    {
        if($view === 'login' || $view === 'register'){
            $layout = 'auth';
        }
        else{
            $layout = 'main';
        }
        $post = false;
        if($view === 'post'){
            $post = true;
        }

        $layoutContent = $this->layoutContent($layout, $post);
        $onlyView = $this->renderOnlyView($view, $params);
        $style = "$view/$view.css";

//        return str_replace("{{style}}",$onlyView,$layoutContent);
        return str_replace(
            array("{{style}}","{{content}}"),
            array($style, $onlyView),
            $layoutContent
        );
    }


    private function layoutContent($layout , $post = false)
    {
        ob_start();
        include_once Application::$ROOT."/views/layouts/$layout.php";
        return ob_get_clean();
    }
    private function renderOnlyView($view, $params){
        extract($params);

        ob_start();
        include_once Application::$ROOT."/views/$view.php";
        return ob_get_clean();
    }
}