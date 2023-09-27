<?php

namespace aspsierra\phpBasicFw;

class Request
{
    /**
     * Retrieve current url
     * @return  mixed
     */
    public function getPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        if ($position === false) {
            return $path;
        }
        return substr($path, 0, $position);
    }

    /**
     * Retrive current method
     * @return  string
     */
    public function method()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Checks if the current mehod is get
     * @return  bool
     */
    public function isGet()
    {
        return $this->method() === 'get';
    }

    /**
     * Checks if the current mehod is post
     * @return  bool
     */
    public function isPost()
    {
        return $this->method() === 'post';
    }

    /**
     * Obtain submitted data
     * @return  array   
     */
    public function getBody()
    {
        $body = [];
        if ($this->method() === 'get') {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        if ($this->method() === 'post') {
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        return $body;
    }
}
