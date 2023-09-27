<?php

namespace aspfw\app\core\exception;

class NotFoudException extends \Exception{
    protected $code = 404;
    protected $message = 'Page not found';
}