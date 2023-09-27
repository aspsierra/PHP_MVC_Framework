<?php

namespace aspsierra\phpBasicFw;

use aspsierra\phpBasicFw\Application;
use aspsierra\phpBasicFw\exception\NotFoudException;

class Router
{

    protected array $routes = [];
    public Request $request;
    public Response $response;


    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Access to the specified url via GET
     * @param   string  $path      route
     * @param   array   $callback  controller and function name that handles route
     */
    public function get($path, $callback)
    {
        $this->routes['get'][$path] = $callback;
    }

    /**
     * Access to the specified url via POST
     * @param   string  $path      route
     * @param   array   $callback  controller and function name that handles route
     */
    public function post($path, $callback)
    {
        $this->routes['post'][$path] = $callback;
    }

    /**
     * verify if the route exists and redirect to it
     */
    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->method();
        //Verify if the url exists and is defined in the app
        $callback = $this->routes[$method][$path] ?? false;

        if ($callback === false) {
            throw new NotFoudException();
        }
        if (is_string($callback)) {
            return Application::$app->view->renderView($callback);
        }
        if (is_array($callback)) {
            Application::$app->setController(new $callback[0]());
            Application::$app->getController()->action = $callback[1];

            foreach (Application::$app->getController()->getMiddlewares() as $middleware) {
                $middleware->execute();
            }
            $callback[0] = Application::$app->getController();
        }
        call_user_func($callback, $this->request, $this->response);
    }
}
