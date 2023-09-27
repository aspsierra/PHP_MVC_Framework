<?php

namespace aspsierra\phpBasicFw\core;

class View
{
    public string $title = '';

    public function renderView($view, $params = []){
        $viewContent = $this->viewContent($view, $params);
        $layout = $this->layoutContent();
        return str_replace('{{content}}', $viewContent, $layout);
    }

    public function renderError($error){
        $layout = $this->layoutContent();
        return str_replace('{{content}}', $error, $layout);
    }

    protected function layoutContent(){

        $layout =  Application::$app->layout;
        if(Application::$app->getController()){
            $layout = Application::$app->getController()->layout;
        }
        ob_start();
        include_once Application::$ROOT_DIR."/app/views/layouts/$layout.php";
        return ob_get_clean();
    }

    protected function viewContent($view, $params){
        foreach ($params as $key => $value) {
            $$key = $value;
        }
        
        ob_start();
        include_once Application::$ROOT_DIR."./app/views/$view.php";
        return ob_get_clean();
    }
}
