<?php

namespace aspsierra\phpBasicFw\middlewares;

use aspsierra\phpBasicFw\Application;
use aspsierra\phpBasicFw\exception\ForbiddenException;
use aspsierra\phpBasicFw\middlewares\Middleware;

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
