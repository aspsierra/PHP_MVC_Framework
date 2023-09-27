<?php

namespace aspsierra\phpBasicFw\core;

use aspsierra\phpBasicFw\core\middlewares\Middleware;
/**
 * Controlador básico
 */
class Controller{

    public string $layout = 'main';
    public string $action = '';

    /**
     * @var Middleware[]
     */
    public array $middlewares = [];

    /**
     * Cargar una vista
     * @param   string  $view   Nombre de la vista a cargar
     * @param   array  $params  Parámetros para cargar la vista
     */
    public function render($view, $params = []){
        echo Application::$app->view->renderView($view, $params);
    }

    /**
     * Asignar plantilla
     * @param   string  $layout  nombre de la plantilla
     */
    public function setLayout($layout){
        $this->layout = $layout;
    }

    /**
     * Aplica el middleware especificado
     * @param   Middleware  $middleware  Middleware a aplicar
     */
    public function registerMiddleware(Middleware $middleware){
       $this->middlewares[] = $middleware;
    }

    /**
     * Obtener middlewares activos
     * @return  array
     */
    public function getMiddlewares(){
        return $this->middlewares;
    }
}