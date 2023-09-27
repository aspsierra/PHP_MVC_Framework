<?php

namespace aspsierra\phpBasicFw\core;

use aspsierra\phpBasicFw\core\Router;
use aspsierra\phpBasicFw\core\Request;
use aspsierra\phpBasicFw\core\Response;
use aspsierra\phpBasicFw\core\Controller;
use aspsierra\phpBasicFw\core\database\Database;
use aspsierra\phpBasicFw\core\Session;
use aspsierra\phpBasicFw\core\database\DbModel;
use aspsierra\phpBasicFw\core\View;

/**
 * Logica principal de la app
 */
class Application
{

    public string $layout = 'main';
    public string $userClass;
    public Router $router;
    public Request $request;
    public Response $response;
    public Session $session;
    public Database $db;
    public ?DbModel $user;
    public View $view;

    public static string $ROOT_DIR;
    public static Application $app;
    public ?Controller $controller = null;

    public function __construct($rootPath, array $config)
    {
        self::$ROOT_DIR = $rootPath;
        //Guardo la app dentro de ella misma para hacer accesibles los detos
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router($this->request, $this->response);
        $this->view = new View();

        $this->db = new Database($config['db']);
        $this->userClass = $config['userClass'];

        //Busco si hay alguna sessión activa de un user
        if ($primaryValue = $this->session->get('user')) {
            $primaryKey = $this->userClass::primaryKey();
            $this->user = $this->userClass::findOne([$primaryKey => $primaryValue]);
        } else {
            $this->user = null;
        }
    }
    /**
     * arrancar aplicación
     * @return  
     */
    public function run()
    {
        try {
            echo $this->router->resolve();
        } catch (\Exception $ex) {
            $this->response->setStatusCode($ex->getCode());
            echo $this->view->renderView('errors/error', [
                'exception' => $ex
            ]);
        }
    }

    /**
     * Obtener controlador actual
     * @return  Controller
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Asignar controlador
     * @param   Controller  $controller  [$controller description]
     * @return  
     */
    public function setController(Controller $controller)
    {
        $this->controller = $controller;
    }

    /**
     * Loguear un usuario
     * @param   UserModel  $user 
     * @return  bool
     */
    public function login(UserModel $user)
    {
        $this->user = $user;
        $primaryKey = $user->primaryKey();
        $primaryValue = $user->{$primaryKey};

        $this->session->set('user', $primaryValue);

        return true;
    }

    /**
     * Cerrar sesión de usuario
     * @return  [type]  [return description]
     */
    public function logout()
    {
        $this->user = null;
        $this->session->remove('user');
    }

    /**
     * Determinar si el usuario tiene una sesión activa o no
     * @return  bool
     */
    public static function isGuest()
    {
        if (isset(self::$app->user)) {
            return false;
        }
        return true;
    }
}
