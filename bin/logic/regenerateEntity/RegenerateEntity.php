<?php

namespace Bin\Logic\RegenerateEntity;

//$files = scandir($path);
/*Do this at the end.

// Take all the entities Class -> then if you know the name of the class you can get all the entity props by public ColumnSetup variable
//Then you have to store these columns and their data in variables 
//After that you have to regenerate the Entity.php itself, regenerate EMM(EntityMigrationManager)Entity.php and GetMigrationData.php
*/

class RegenerateEntity
{
    private $_columns = [], $_entities = [];
    public function __construct()
    {
        $this->_entities = scandir(ENTITY_PATH);
        if ($this->_entities[0] === "." || $this->_entities[0] === "..") {
            array_shift($this->_entities);
        }
        if ($this->_entities[0] === "." || $this->_entities[0] === "..") {
            array_shift($this->_entities);
        }


        $fileContentsNewCreateMigration = '';
        $pathCreateMigration = ROOT . DS . "bin" . DS . "logic" . DS . "createMigration" . DS . "GetMigrationData.php";

        $fileContentsNewCreateMigration .= '<?php
namespace Bin\Logic\CreateMigration;
                
class GetMigrationData
    {
        private $_columnsArray = [];
        public function __construct()
        {
            return $this->_columnsArray;
        }
    }';
        file_put_contents($pathCreateMigration, $fileContentsNewCreateMigration);

        foreach ($this->_entities as $entity) {
            //catch all the columns inside the entity and set it for a base template
            $entityClassName = rtrim($entity, ".php");
            $entityPath = ENTITY_PATH . DS . $entity;
            $namespace = '\App\Entities\\' . $entityClassName;
            $entityColumnSetup = get_class_vars(get_class(new $namespace));

            $entityBaseContent =
                '<?php

namespace App\Entities;

class ' . $entityClassName . '{
    
}';
            file_put_contents($entityPath, $entityBaseContent);


            //Regenerate the EntityMigrationManager - EMMREGENERATE
            $EMMPath = EMM_PATH . DS . "EMM" . $entityClassName . ".php";
            $EMMBaseContent =
                '<?php

namespace Bin\Logic\EntityMigrationManager;

use App\Entities\\' . $entityClassName . ';


class EMM' . $entityClassName . ' extends ' . $entityClassName . '{
    
}';
            file_put_contents($EMMPath, $EMMBaseContent);



            //Regenerate GETMIGRATIONDATA file in migrations
            $fileContentsNewCreateMigration = '';

            $fileContentsCreateMigration = rtrim(trim(rtrim(trim(rtrim(trim(file_get_contents(ROOT . DS . "bin" . DS . "logic" . DS . "createMigration" . DS . "GetMigrationData.php")), '}')), '}')),  'return $this->_columnsArray;');
            $fileContentsNewCreateMigration .= $fileContentsCreateMigration . '
    $' . $entityClassName . ' = new \Bin\Logic\EntityMigrationManager\EMM' . $entityClassName . ';
    return $this->_columnsArray;
    }
}';

            file_put_contents($pathCreateMigration, $fileContentsNewCreateMigration);

            foreach ($entityColumnSetup as $column => $setup) {
                //regenerate the entity itself
                $columnName = rtrim(rtrim($column, 'olumnSetup'), "C");
                $entityStockContent = rtrim(trim(file_get_contents($entityPath)), "}");


                //-------------Createt the setup string (specification of the column like Type, Length etc)----------------//
                $setupString = "";
                foreach ($setup as $propDetails => $value) {
                    $setupString .= '"' . $propDetails . '"' . "=>" . ((is_int($value)) ? $value : '"' . $value . '"') . ",
        ";
                }
                $setupStringFinal = rtrim(trim($setupString), ",");



                //create the content for the properity/column and insert it into the file
                $entityBaseContentNew = $this->addPropsToEntity($entityStockContent, $columnName, $column, $setupStringFinal);

                file_put_contents($entityPath, $entityBaseContentNew);




                //add getters into EMM file

                $baseEMMContent = rtrim(trim(file_get_contents($EMMPath)), "}");
                $newEmmContent = $baseEMMContent . '
    public function get' . ucwords($column) . '(){
        return $this->' . $column . ';
    }
}';
                file_put_contents($EMMPath, $newEmmContent);


                //add properities into GETMIGRATIONDATA
                //$this->_columnsArray["' . lcfirst($this->_entityName) . '"] = ["id" => $' . $this->_entityName . '->getIdColumnSetup()];
                $fileContentsCreateMigration = rtrim(trim(rtrim(trim(rtrim(trim(file_get_contents(ROOT . DS . "bin" . DS . "logic" . DS . "createMigration" . DS . "GetMigrationData.php")), '}')), '}')),  'return $this->_columnsArray;');
                $MigrationDataNewContent = $fileContentsCreateMigration . '
        $this->_columnsArray["' . lcfirst($entityClassName) . '"]["' . $columnName . '"] = $' . $entityClassName . '->get' . ucwords($column) . '();
        return $this->_columnsArray;
    }
}';
                file_put_contents($pathCreateMigration, $MigrationDataNewContent);
            }
        }
    }



    private function addPropsToEntity($entityStockContent, $columnName, $column, $setupString)
    {

        $entityBaseContentNew = $entityStockContent . '
    private $' . $columnName . ';
    public $' . $column . ' = [
        ' . $setupString . '
    ];
    
    public function get' . ucwords($columnName) . '(){
        return $this->' . $columnName . ';
    }

    ' . (($columnName !== "id") ? 'public function set' . ucwords($columnName) . '($' . $columnName . '){
        $this->' . $columnName . ' = $' . $columnName . ';
    }' : '') . '
}';

        return $entityBaseContentNew;
    }
}
