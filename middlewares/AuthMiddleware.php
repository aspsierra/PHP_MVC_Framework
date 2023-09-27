<?php

namespace aspsierra\phpBasicFw\core\middlewares;

use aspsierra\phpBasicFw\core\Application;
use aspsierra\phpBasicFw\core\exception\ForbiddenException;
use aspsierra\phpBasicFw\core\middlewares\Middleware;

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
