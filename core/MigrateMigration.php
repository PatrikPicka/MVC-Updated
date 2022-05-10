<?php

namespace Core;

use Core\DB;

class MigrateMigration
{
    private $_SQL = "";
    protected function createSQL($sql)
    {
        $this->_SQL .= $sql;
    }

    protected function migrate()
    {
        $db = DB::getInstance();

        if (!$db->query($this->_SQL)->error()) {
            return true;
        } else {
            return false;
        }
    }
}
