<?php

namespace Bin\Logic\CreateEntity;


require_once(ROOT . DS . 'config' . DS . 'config.php');

class CreateEntity
{
    private $_entityName, $_propName, $_propType, $_propLength, $_nullable, $_successMsg = "";
    private $_types = ["int", "tinyint", "bigint", "string", "text", "blob", "bool", "date", "datetime", "year", "timestamp", "json", "relation"];

    public function createEntity()
    {
        echo "\033[92mChose name of your entity:";
        $handle = fopen("php://stdin", "r");
        $this->_entityName = ucwords(trim(fgets($handle)));
        while ($this->_entityName === "") {
            echo "\033[33mName of the entity cannot be blank.\n";
            echo "\033[92mChose name of your entity:";
            $handle = fopen("php://stdin", "r");
            $this->_entityName = ucwords(trim(fgets($handle)));
        }
        //--------Create the entity class and display success message-----------//
        if ($this->createEntityClass()) {
            $this->_successMsg = "\n\n\033[92mEntity created successfuly.\n";
        } else {
            $this->_successMsg = "\n\033[92mEntity already exists. You can now add your new properties.\n";
        }
        if ($this->_successMsg !== "") {
            echo "\033[92m" . $this->_successMsg . "\n";
        }

        $this->_successMsg = "\n\n\033[92mSuccess! \nEntity successfuly updated.\n";


        //--------Add props to the the entity class--------//
        echo "\033[92mDo you want to add any properties?[yes/no]";
        $handleNewColumn = fopen("php://stdin", "newColumn");
        $columnInput = trim(fgets($handleNewColumn));
        if ($columnInput === "") {
            $columnInput = "yes";
        }

        //----------check if the the user wants to add new properities----------/

        while ($columnInput !== "") {
            if ($columnInput !== "no") {
                echo "\033[92mName of the properity:";
                $addProperity = fopen("php://stdin", "newProperity");
                $newProperity = trim(fgets($addProperity));
                //check if the user wants to add ID which is already defined
                while ($newProperity === "id") {
                    echo "\033[33mID is already defined.\n";
                    echo "\033[92mName of the properity:";
                    $handleId = fopen("php://stdin", "handleId");
                    $newProperity = trim(fgets($handleId));
                }
                //check if the properity already exists
                while ($this->properityExists($newProperity) === true) {
                    echo "\033[33mProperity already exists.\n";
                    echo "\033[92mSelect new properity:";
                    $handlePropExists = fopen("php://stdin", "handlePropExists");
                    $newProperity = trim(fgets($handlePropExists));
                }
                //if the user leaves the properity name blank exit the app
                if ($newProperity === "") {
                    echo $this->_successMsg;
                    exit;
                }

                //set recomended type
                $recomendedType = $this->setRecomendedType($newProperity);


                //select the properity type - [int, string, text, bool, date, relations, arrays]
                echo "\033[92mProperity type?[" . $recomendedType . "]";
                $handleType = fopen("php://stdin", "type");
                $newType = trim(fgets($handleType));
                if ($newType === "") {
                    $newType = $recomendedType;
                }

                //check if the properity type exists or if user is asking for help
                while ($this->checkIfTypeExists($newType) === false) {
                    if ($newType == "help" || $newType == "-help" || $newType == "-h" || $newType == "h") {
                        echo "\033[92mAvaliable options are: \n";
                        foreach ($this->_types as $type) {
                            echo "\033[33m" . $type . "\n";
                        }
                        echo "\033[92mProperity type?";
                        $handleTypeHelp = fopen("php://stdin", "typeHelp");
                        $newType = trim(fgets($handleTypeHelp));
                    } else {
                        echo "\033[33mThis type is not avaliable.\n";
                        echo "\033[33mWrite '-help' for more info.\n";
                        echo "\033[92mProperity type?";
                        $handleTypeDoesNotExist = fopen("php://stdin", "typeDoesNotExist");
                        $newType = trim(fgets($handleTypeDoesNotExist));
                    }
                }

                //check if the type is or is not relation
                if ($newType !== "relation") {
                    $recomendedLength = $this->setRecomendedLength($newType);

                    //chose the length of the properity if the type is int or string(varchar)
                    if ($recomendedLength !== "skip") {
                        echo "\033[92mLength?[" . $recomendedLength . "]";
                        $handleLength = fopen("php://stdin", "length");
                        $newLength = trim(fgets($handleLength));
                        if ($newLength === "") {
                            $newLength = $recomendedLength;
                        } elseif (is_numeric($newLength)) {
                            intval($newLength);
                        } else {
                            while (!is_numeric($newLength)) {
                                echo "\033[33mLength has to be a number not a string.\n";
                                echo "\033[92mProperity length?";
                                $handleLengthIsString = fopen("php://stdin", "handleLengthIsString");
                                $newLength = trim(fgets($handleLengthIsString));
                            }
                        }
                    } else {
                        $newLength = '"' . $recomendedLength . '"';
                    }

                    $recomendedNullText = "false";

                    echo "\033[92mNullable?[" . $recomendedNullText . "]";
                    $handleNull = fopen("php://stdin", "null");
                    $newNull = trim(fgets($handleNull));
                    if ($newNull !== "true") {
                        $addNullParam = 0;
                    } else {
                        $addNullParam = 1;
                    }

                    $this->addProperities($newProperity, $newType, $newLength, $addNullParam);

                    echo "\033[92mProperity added. Do you want to add another properity?";
                    $addAnotherColumn = fopen("php://stdin", "addAnotherColumn");
                    $columnInput = trim(fgets($addAnotherColumn));
                    if ($columnInput === "") {
                        $columnInput = "yes";
                    }
                } elseif ($newType === "relation") {
                    echo "\033[92mOn which class/entity should this relation reference?";
                    $handleReference = fopen("php://stdin", "relationReference");
                    $newReference = trim(fgets($handleReference));
                    while (!file_exists(ENTITY_PATH . DS . ucwords($newReference) . ".php")) {
                        echo "\033[33mEntity was not found!\n";
                        echo "\033[92mIf you want to add the properity later write YES. Else please specify the entity class name.";
                        $handleEntityNotFound = fopen("php://stdin", "handleEntityNotFound");
                        $newReference = trim(fgets($handleEntityNotFound));
                        if ($newReference === "YES") {
                            $newReference = "entityWillBeAddedAfter";
                        }
                    }

                    $recomendedNullText = "false";

                    echo "\033[92mNullable?[" . $recomendedNullText . "]";
                    $handleNull = fopen("php://stdin", "null");
                    $newNull = trim(fgets($handleNull));
                    if ($newNull !== "true") {
                        $addNullParam = 0;
                    } else {
                        $addNullParam = 1;
                    }

                    $this->addRelation($newProperity, $newType, $newReference, $addNullParam);
                    echo "\033[92mSuccess! Relation successfuly created!";
                    echo "\033[92mDo you want to add another properity?";
                    $addAnotherColumn = fopen("php://stdin", "addAnotherColumn");
                    $columnInput = trim(fgets($addAnotherColumn));
                }
            } else {
                echo $this->_successMsg;
                echo "\33[39m";
                exit;
            }
        }
        echo $this->_successMsg;
        echo "\33[39m";
        exit;
    }



    //___________________________Create the entity if the entity does not exists already______________________________//
    private function createEntityClass()
    {
        $path = ENTITY_PATH . DS . $this->_entityName . ".php";
        if (!file_exists($path)) {
            $content = '<?php
namespace App\Entities;  
class ' . $this->_entityName . ' {
    //Please do not change the ID column.
    private $id;
    public $idColumnSetup = [
        "type" => "int",
        "length" => 11,
        "autoIncrement" => true,
        "index" => "primary"
    ];
    public function getId()
    {
        return $this->id;
    }
}';
            file_put_contents($path, $content);

            //-----------Create the entity migration manager for the entity--------------/

            $pathForEMM = EMM_PATH . DS . "EMM" . $this->_entityName . ".php";
            if (!file_exists($pathForEMM)) {
                $contentEMM = '<?php
namespace Bin\Logic\EntityMigrationManager;

use App\Entities\\' . $this->_entityName . ';

class EMM' . $this->_entityName . ' extends ' . $this->_entityName . '{
    public function getIdColumnSetup()
    {
        return $this->idColumnSetup;
    }
}
';
            }
            file_put_contents($pathForEMM, $contentEMM);


            $fileContentsNewCreateMigration = '';
            $pathCreateMigration = ROOT . DS . "bin" . DS . "logic" . DS . "createMigration" . DS . "GetMigrationData.php";



            //---------------if migration file does not exists create it and add base prop and the entity into it.---------------------//

            if (!file_exists($pathCreateMigration)) {
                $fileContentsNewCreateMigration .= '<?php
namespace Bin\Logic\CreateMigration;
                
class GetMigrationData
    {
        private $_columnsArray = [];
        public function __construct()
        {
            $' . $this->_entityName . ' = new \Bin\Logic\EntityMigrationManager\EMM' . $this->_entityName . ';
            $this->_columnsArray["' . lcfirst($this->_entityName) . '"] = ["id" => $' . $this->_entityName . '->getIdColumnSetup()];
            return $this->_columnsArray;
        }
    }';
            } elseif (file_exists($pathCreateMigration)) {
                $fileContentsCreateMigration = rtrim(trim(rtrim(trim(rtrim(trim(file_get_contents(ROOT . DS . "bin" . DS . "logic" . DS . "createMigration" . DS . "GetMigrationData.php")), '}')), '}')),  'return $this->_columnsArray;');
                $fileContentsNewCreateMigration .= $fileContentsCreateMigration . '
    $' . $this->_entityName . ' = new \Bin\Logic\EntityMigrationManager\EMM' . $this->_entityName . ';
    $this->_columnsArray["' . lcfirst($this->_entityName) . '"] = ["id" => $' . $this->_entityName . '->getIdColumnSetup()];
    return $this->_columnsArray;
    }
}';
            }

            file_put_contents($pathCreateMigration, $fileContentsNewCreateMigration);
            return true;
        }
        return false;
    }



    //___________________________Add properities______________________________//
    private function addProperities($columnName, $type, $length, $null)
    {
        //---------------Add props to the entity itself----------//
        $fileContentsNew = '';
        $fileContents = rtrim(trim(file_get_contents(ENTITY_PATH . DS . $this->_entityName . ".php")), '}');
        $path = ENTITY_PATH . DS . $this->_entityName . ".php";
        $fileContentsNew .= $fileContents . '
    private $' . $columnName . ';
    public $' . $columnName . 'ColumnSetup = [
            "type" => "' . $type . '",
            "length" => ' . $length . ',
            "nullable" => ' . $null . '
    ];

    public function get' . ucwords($columnName) . '() {
        return $this->' . $columnName . ';
    }

    public function set' . ucwords($columnName) . '($' . $columnName . '){
        $this->' . $columnName . ' = $' . $columnName . ';
    return $this;
    }
}';
        file_put_contents($path, $fileContentsNew);


        //-----------------EntityMigrationManager-------------//
        $fileContentsNewEMM = '';
        $fileContentsEMM = rtrim(trim(file_get_contents(EMM_PATH . DS . "EMM" . $this->_entityName . ".php")), '}');
        $pathEMM = EMM_PATH . DS . "EMM" . $this->_entityName . ".php";
        $fileContentsNewEMM .= $fileContentsEMM . '
    public function get' . ucwords($columnName) . 'ColumnSetup()
    {
        return $this->' . $columnName . 'ColumnSetup;
    }

}';
        file_put_contents($pathEMM, $fileContentsNewEMM);


        //------------------Add new props to the migration creator----------//
        $fileContentsNewCreateMigration = '';
        $fileContentsCreateMigration = rtrim(trim(rtrim(trim(rtrim(trim(file_get_contents(ROOT . DS . "bin" . DS . "logic" . DS . "createMigration" . DS . "GetMigrationData.php")), '}')), '}')), 'return $this->_columnsArray;');
        $pathCreateMigration = ROOT . DS . "bin" . DS . "logic" . DS . "createMigration" . DS . "GetMigrationData.php";
        $fileContentsNewCreateMigration .= $fileContentsCreateMigration . '
        $this->_columnsArray["' . lcfirst($this->_entityName) . '"]["' . $columnName . '"] = $' . $this->_entityName . '->get' . ucwords($columnName) . 'ColumnSetup();
        return $this->_columnsArray;
            }
        }';
        file_put_contents($pathCreateMigration, $fileContentsNewCreateMigration);
    }



    private function addRelation($columnName, $type, $reference, $nullable)
    {
        //---------------Add props to the entity itself----------//
        $fileContentsNew = '';
        $fileContents = rtrim(trim(file_get_contents(ENTITY_PATH . DS . $this->_entityName . ".php")), '}');
        $path = ENTITY_PATH . DS . $this->_entityName . ".php";
        $fileContentsNew .= $fileContents . '
    private $' . $columnName . ';
    public $' . $columnName . 'ColumnSetup = [
            "' . $type . '" => "' . $reference . '",
            "type" => "int",
            "constraint" => "' . $columnName . '",
            "length" => 11,
            "nullable" => ' . $nullable . '
    ];

    public function get' . ucwords($columnName) . 'RelationClass() {
        return $this->' . $columnName . ';
    }

    public function set' . ucwords($columnName) . 'RelationClass($' . $columnName . '){
        $this->' . $columnName . ' = $' . $columnName . ';
    return $this;
    }
}';
        file_put_contents($path, $fileContentsNew);


        //-----------------EntityMigrationManager-------------//
        $fileContentsNewEMM = '';
        $fileContentsEMM = rtrim(trim(file_get_contents(EMM_PATH . DS . "EMM" . $this->_entityName . ".php")), '}');
        $pathEMM = EMM_PATH . DS . "EMM" . $this->_entityName . ".php";
        $fileContentsNewEMM .= $fileContentsEMM . '
    public function get' . ucwords($columnName) . 'ColumnSetup()
    {
        return $this->' . $columnName . 'ColumnSetup;
    }

}';
        file_put_contents($pathEMM, $fileContentsNewEMM);


        //------------------Add new props to the migration creator----------//
        $fileContentsNewCreateMigration = '';
        $fileContentsCreateMigration = rtrim(trim(rtrim(trim(rtrim(trim(file_get_contents(ROOT . DS . "bin" . DS . "logic" . DS . "createMigration" . DS . "GetMigrationData.php")), '}')), '}')), 'return $this->_columnsArray;');
        $pathCreateMigration = ROOT . DS . "bin" . DS . "logic" . DS . "createMigration" . DS . "GetMigrationData.php";
        $fileContentsNewCreateMigration .= $fileContentsCreateMigration . '
        $this->_columnsArray["' . lcfirst($this->_entityName) . '"]["' . $columnName . '"] = $' . $this->_entityName . '->get' . ucwords($columnName) . 'ColumnSetup();
        return $this->_columnsArray;
            }
        }';
        file_put_contents($pathCreateMigration, $fileContentsNewCreateMigration);
    }



    private function properityExists($columnName)
    {
        if ($columnName !== "") {
            $fileContents = trim(file_get_contents(ENTITY_PATH . DS . $this->_entityName . ".php"));
            if (strpos($fileContents, "private $" . $columnName) !== false) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function checkIfTypeExists($type)
    {
        $returnBool = false;
        for ($x = 0; $x < count($this->_types); $x++) {
            if ($type === $this->_types[$x]) {
                $returnBool = true;
            }
        }
        return $returnBool;
    }

    private function setRecomendedType($newProperity)
    {
        if (str_contains($newProperity, "id") || str_contains($newProperity, "Id")) {
            $recomendedType = "int";
        } elseif (str_contains($newProperity, "date") || str_contains($newProperity, "Date")) {
            $recomendedType = "datetime";
        } elseif (str_contains($newProperity, "created") || str_contains($newProperity, "Created")) {
            $recomendedType = "timestamp";
        } elseif (str_starts_with($newProperity, "is") || str_starts_with($newProperity, "_is") || str_starts_with($newProperity, "Is")) {
            $recomendedType = "bool";
        } else {
            $recomendedType = "string";
        }
        return $recomendedType;
    }

    private function setRecomendedLength($type)
    {
        $returnLength = "skip";
        if ($type === "string") {
            $returnLength = 255;
        } elseif ($type === "int") {
            $returnLength = 11;
        }

        return $returnLength;
    }
}
