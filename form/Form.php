<?php

namespace aspsierra\phpBasicFw\core\form;

use aspsierra\phpBasicFw\core\Model;

class Form{
    public static function begin($action, $method){
        echo sprintf('<form action = "%s" method = "%s" >', $action, $method);
        return new Form();
    }
    public static function end(){
        return '</form>';
    }
    public function field(Model $model, $attribute){
        return new InputField($model, $attribute);
    }
}