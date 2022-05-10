<?php

namespace Bin\Logic\CreateMigration;

use Bin\Logic\CreateMigration\CreateMigrationSQL;

class GenerateMigration extends CreateMigrationSQL
{
    private $_migrationFileName, $_pathToMigrationUp, $_pathToMigrationDown, $_tablesArray, $_columnsArray, $_relationsArray, $_deleteTablesArray, $_deleteColumnsArray, $_deleteRelationsArray;
    public function __construct()
    {
        parent::__construct();
        $this->_migrationFileName = "Migration" . time() . ".php";
        $this->_pathToMigrationUp = ROOT . DS . "migrations" . DS . "up" . DS . $this->_migrationFileName;
        $this->_pathToMigrationDown = ROOT . DS . "migrations" . DS . "down" . DS . $this->_migrationFileName;
        //MIGRATION UP SQLs 
        $this->_tablesArray = $this->getTablesSQL();
        $this->_columnsArray = $this->getColumnSQL();
        $this->_relationsArray = $this->getRelationSQL();

        //MIGRATION DOWN SQLs
        $this->_deleteTablesArray = $this->getDeleteTableSQL();
        $this->_deleteColumnsArray = $this->getDeleteColumnSQL();
        $this->_deleteRelationsArray = $this->getDeleteRelationSQL();


        if ($this->_tablesArray === [] && $this->_columnsArray === [] && $this->_relationsArray === []) {
            echo "\033[33mThere was a problem creating your migration file. Probably you forget to regenerate your entities(regenerate:entity).\n\n";
            echo "\33[39m";
        } else {
            $baseContentUp = '<?php
namespace Migrations\Up;

use Core\MigrateMigration;

class ' . rtrim($this->_migrationFileName, ".php") . ' extends MigrateMigration{
    public function __construct(){
    }
}';


            $baseContentDown = '<?php
namespace Migrations\Down;

use Core\MigrateMigration;
        
class ' . rtrim($this->_migrationFileName, ".php") . ' extends MigrateMigration{
    public function __construct(){
    }
}';
            file_put_contents($this->_pathToMigrationUp, $baseContentUp);
            file_put_contents($this->_pathToMigrationDown, $baseContentDown);
        }
    }


    public function generateMigrations()
    {
        if (file_exists($this->_pathToMigrationUp) && file_exists($this->_pathToMigrationDown)) {

            //Migration UP QUERY SETTER
            foreach ($this->_tablesArray as $tableSQL) {
                $baseContentUp = rtrim(trim(rtrim(trim(rtrim(file_get_contents($this->_pathToMigrationUp), "}")), "}")), '$this->migrate();');
                $newContentUp = $baseContentUp . '
        $this->createSQL("' . $tableSQL . '");
        $this->migrate();
    }
}';
                file_put_contents($this->_pathToMigrationUp, $newContentUp);
            }

            foreach ($this->_columnsArray as $columnSQL) {
                $baseContentUp = rtrim(trim(rtrim(trim(rtrim(file_get_contents($this->_pathToMigrationUp), "}")), "}")), '$this->migrate();');
                $newContentUp = $baseContentUp . '
        $this->createSQL("' . $columnSQL . '");
        $this->migrate();
    }
}';
                file_put_contents($this->_pathToMigrationUp, $newContentUp);
            }

            foreach ($this->_relationsArray as $relationSQL) {
                $baseContentUp = rtrim(trim(rtrim(trim(rtrim(file_get_contents($this->_pathToMigrationUp), "}")), "}")), '$this->migrate();');
                $newContentUp = $baseContentUp . '
        $this->createSQL("' . $relationSQL . '");
        $this->migrate();
    }
}';
                file_put_contents($this->_pathToMigrationUp, $newContentUp);
            }

            //Migration DOWN QUERY SETTER
            foreach ($this->_deleteRelationsArray as $deleteRelation) {
                $baseContentDown = rtrim(trim(rtrim(trim(rtrim(file_get_contents($this->_pathToMigrationDown), "}")), "}")), '$this->migrate();');
                $newContentDown = $baseContentDown . '
        $this->createSQL("' . $deleteRelation . '");
        $this->migrate();
    }
}';
                file_put_contents($this->_pathToMigrationDown, $newContentDown);
            }

            foreach ($this->_deleteColumnsArray as $deleteColumn) {
                $baseContentDown = rtrim(trim(rtrim(trim(rtrim(file_get_contents($this->_pathToMigrationDown), "}")), "}")), '$this->migrate();');
                $newContentDown = $baseContentDown . '
        $this->createSQL("' . $deleteColumn . '");
        $this->migrate();
    }
}';
                file_put_contents($this->_pathToMigrationDown, $newContentDown);
            }

            foreach ($this->_deleteTablesArray as $deleteTable) {
                $baseContentDown = rtrim(trim(rtrim(trim(rtrim(file_get_contents($this->_pathToMigrationDown), "}")), "}")), '$this->migrate();');
                $newContentDown = $baseContentDown . '
        $this->createSQL("' . $deleteTable . '");
        $this->migrate();
    }
}';
                file_put_contents($this->_pathToMigrationDown, $newContentDown);
            }

            return true;
        } else {
            return false;
        }
    }
}
