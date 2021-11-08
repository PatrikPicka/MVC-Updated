<?php

use Core\DB;

class UserController
{

    public function __construct($params = null)
    {

        if (isset($params)) {
            $id = $params[0];
            echo $id . " setted in contruct function";
        } else {
            echo "id not setted in contruct function";
        }
    }

    public function indexAction($id)
    {
        echo $id . " in index action";
    }
}
