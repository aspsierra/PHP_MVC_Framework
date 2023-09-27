<?php

namespace aspfw\app\core\middlewares;

use aspfw\app\core\Application;
use aspfw\app\core\exception\ForbiddenException;
use aspfw\app\core\middlewares\Middleware;

class AuthMiddleware extends Middleware
{
    public array $actions = [];

    public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }

    public function execute(){
        if (Application::isGuest()){
            if(empty($this->actions) || in_array(Application::$app->getController()->action, $this->actions)){
                throw new ForbiddenException();
            }
        }
    }
}
