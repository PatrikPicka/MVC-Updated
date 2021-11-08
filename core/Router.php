<?php

namespace Core;

class Router
{

    public static function route($url)
    {
        if (!isset($url[0]) || $url[0] === "") {
            require_once (ROOT . DS . "app" . DS . "controllers" . DS . DEFAULT_VIEW) . ".php";

            $controller = DEFAULT_VIEW;

            $action = "indexAction";

            $dispatch = new $controller();
            if (method_exists($controller, $action)) {
                call_user_func([$dispatch, $action]);
            }
        } elseif (isset($url[0]) && $url[0] !== "api") {

            $controller = ucwords($url[0]) . "Controller";

            array_shift($url);
            $action = (isset($url[0])) ? $url[0] . "Action" : "indexAction";

            array_shift($url);
            $params = $url;

            if (file_exists(ROOT . DS . "app" . DS . "controllers" . DS . $controller . ".php")) {
                require_once(ROOT . DS . "app" . DS . "controllers" . DS . $controller . ".php");
            }

            $dispatch = new $controller();
            if (method_exists($controller, $action)) {
                call_user_func_array([$dispatch, $action], $params);
            } else {
                die('That method does not exists in the controller \"' . $controller . '\"');
            }
            //--------------Routes for backend controllers -> if your app needs a default controller just uncoment the controller if statement and coment the second one
        } elseif (isset($url[0]) && $url[0] . "/" . $url[1] === API_VERSION) {
            array_shift($url);
            array_shift($url);
            /*
            if(!isset($url[0]) || $url[0] === ""){
                $controller = DEFAULT_API_CONTROLLER;
            }else{
                $controller = ucwords($url[0]) . "Controller";
            }
            */
            $controller = ucwords($url[0]) . "Controller";


            array_shift($url);
            $action = null;
            if (isset($url[0]) && !is_numeric($url[0]) && $url[0] !== "") {
                $action =  $url[0] . "Action";
                array_shift($url);
            }

            $params = $url;
            if (file_exists(ROOT . DS . "api" . DS . "v1" . DS . "controllers" . DS . $controller . ".php")) {
                require_once(ROOT . DS . "api" . DS . "v1" . DS . "controllers" . DS . $controller . ".php");
            }
            if (isset($params) && $params !== [] && $params[0] !== "") {
                $dispatch = new $controller($params);
            } else {
                $dispatch = new $controller();
            }

            if (isset($action)) {
                if (method_exists($controller, $action)) {
                    call_user_func_array([$dispatch, $action], $params);
                } else {
                    die('That method does not exists in the controller \"' . $controller . '\"');
                }
            }
        }
    }
}
