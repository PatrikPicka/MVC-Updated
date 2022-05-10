<?php

namespace Bin\Logic\CreateMigration;


use Bin\Logic\CreateMigration\GetMigrationData;

class CreateMigrationSQL
{
  private $_migrationData, $_tables = [], $_addColumnSQL = [], $_createTableSQL = [], $_createRelation = [], $_deleteTableSQL = [], $_deleteColumnSQL = [], $_deleteRelationSQL = [];
  public function __construct()
  {
    $migrationDataOBJ = new GetMigrationData();
    $this->_migrationData = $migrationDataOBJ->__construct();
    $this->CreateQueryFromData();
  }


  /*
  //-----Check if the sql already exists inside of any previous migration and if it exists change the sql to update --- only at columns and relations not in tables tables cannot be changed

  $lastMigrationPathUp = ROOT . DS . "migrations" . DS . "up";
  $migrationsFilesUp = scandir($lastMigrationPathUp, 1);
  $lastMigrationContent = file_get_contents($lastMigrationPathUp . $migrationsFilesUp);
*/

  private function CreateQueryFromData()
  {
    foreach ($this->_migrationData as $table => $columns) {
      $this->_tables[] = $table;
      $createTableSQL = "CREATE TABLE " . $table . " ( id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY );";
      $deleteTableSQL = "DROP TABLE " . $table . ";";
      if (!$this->isDataAlreadyCreated($createTableSQL)) {
        $this->_createTableSQL[] = $createTableSQL;
        $this->_deleteTableSQL[] = $deleteTableSQL;
      }
      foreach ($columns as $column => $columnSetup) {
        if ($column !== "id") {
          if (isset($columnSetup["relation"])) {
            $relatinSQL = "ALTER TABLE " . $table . " ADD " . $columnSetup["constraint"] . " " . $columnSetup["type"] . "(" . $columnSetup["length"] . ") " . ((isset($columnSetup["default"])) ? "DEFAULT " . $columnSetup["default"] . " " : "") . (($columnSetup["nullable"] === 0) ? "NOT NULL;" : "NULL;") . " ALTER TABLE " . $table . " ADD CONSTRAINT " . $columnSetup["constraint"] . " FOREIGN KEY (" . $columnSetup["constraint"] . ") REFERENCES " . $columnSetup["relation"] . "(id);";
            $deleteRelationSQL = "ALTER TABLE " . $table . " DROP FOREIGN KEY " . $columnSetup["constraint"] . ";";
            if ($this->needsToRewriteCodeToUpdateColumn($relatinSQL, $deleteRelationSQL)) {

              //----Dokončit zítra ---- relation check

              //$relatinSQL = "ALTER TABLE " . $table . " MODIFY COLUMN " . $column . " " . (($columnSetup["type"] === "string") ? "varchar" : $columnSetup["type"]) . (($columnSetup["length"] !== "skip") ? "(" . $columnSetup["length"] . ") " : " ") . ((isset($columnSetup["default"])) ? "DEFAULT " . $columnSetup["default"] . " " : "") . (($columnSetup["nullable"] === 1) ? "NULL " : "NOT NULL ") .  ((isset($columnSetup["autoIncrement"])) ? (($columnSetup["autoIncrement"] === true) ? "AUTO_INCREMENT " : "") : "") . ";";
            }
            if (!$this->isDataAlreadyCreated($relatinSQL)) {

              $this->_createRelation[] = $relatinSQL;
              $this->_deleteRelationSQL[] = $deleteRelationSQL;
            }
          } else {
            $addColumnSQL = "ALTER TABLE " . $table . " ADD " . $column . " " . (($columnSetup["type"] === "string") ? "varchar" : $columnSetup["type"]) . (($columnSetup["length"] !== "skip") ? "(" . $columnSetup["length"] . ") " : " ") . ((isset($columnSetup["default"])) ? "DEFAULT " . $columnSetup["default"] . " " : "") . (($columnSetup["nullable"] === 1) ? "NULL " : "NOT NULL ") .  ((isset($columnSetup["autoIncrement"])) ? (($columnSetup["autoIncrement"] === true) ? "AUTO_INCREMENT " : "") : "") . ";";
            $deleteColumnSQL = "ALTER TABLE " . $table . " DROP " . $column . ";";
            if ($this->needsToRewriteCodeToUpdateColumn($addColumnSQL, $deleteColumnSQL)) {
              $addColumnSQL = "ALTER TABLE " . $table . " MODIFY COLUMN " . $column . " " . (($columnSetup["type"] === "string") ? "varchar" : $columnSetup["type"]) . (($columnSetup["length"] !== "skip") ? "(" . $columnSetup["length"] . ") " : " ") . ((isset($columnSetup["default"])) ? "DEFAULT " . $columnSetup["default"] . " " : "") . (($columnSetup["nullable"] === 1) ? "NULL " : "NOT NULL ") .  ((isset($columnSetup["autoIncrement"])) ? (($columnSetup["autoIncrement"] === true) ? "AUTO_INCREMENT " : "") : "") . ";";
            }
            if (!$this->isDataAlreadyCreated($addColumnSQL)) {
              $this->_addColumnSQL[] = $addColumnSQL;
              $this->_deleteColumnSQL[] = $deleteColumnSQL;
            }
          }
        }
      }
    }
  }
  //getters for MIGRATION UP
  protected function getTablesSQL()
  {
    return $this->_createTableSQL;
  }

  protected function getColumnSQL()
  {
    return $this->_addColumnSQL;
  }

  protected function getRelationSQL()
  {
    return $this->_createRelation;
  }

  //getters for MIGRATION DOWN
  protected function getDeleteTableSQL()
  {
    return $this->_deleteTableSQL;
  }
  protected function getDeleteColumnSQL()
  {
    return $this->_deleteColumnSQL;
  }
  protected function getDeleteRelationSQL()
  {
    return $this->_deleteRelationSQL;
  }

  private function isDataAlreadyCreated($sql)
  {
    $migrationsDir = ROOT . DS . "migrations" . DS . "up";
    $files = scandir($migrationsDir);
    if ($files[0] === "." || $files[0] === "..") {
      array_shift($files);
    }
    if ($files[0] === "." || $files[0] === "..") {
      array_shift($files);
    }
    foreach ($files as $migration) {
      $migrationPath = $migrationsDir . DS . $migration;
      $migrationContent = file_get_contents($migrationPath);
      if (str_contains($migrationContent, $sql)) {
        return true;
      }
    }
    return false;
  }


  private function needsToRewriteCodeToUpdateColumn($columnUpSQL, $columnDownSQL)
  {
    $return = false;
    $migrationsUpDir = ROOT . DS . "migrations" . DS . "up";
    $filesUp = scandir($migrationsUpDir);
    if ($filesUp[0] === "." || $filesUp[0] === "..") {
      array_shift($filesUp);
    }
    if ($filesUp[0] === "." || $filesUp[0] === "..") {
      array_shift($filesUp);
    }

    $migrationsDownDir = ROOT . DS . "migrations" . DS . "down";
    $filesDown = scandir($migrationsDownDir);
    if ($filesDown[0] === "." || $filesDown[0] === "..") {
      array_shift($filesDown);
    }
    if ($filesDown[0] === "." || $filesDown[0] === "..") {
      array_shift($filesDown);
    }

    foreach ($filesUp as $migrationUp) {
      $migrationUpPath = $migrationsUpDir . DS . $migrationUp;
      if (str_contains($migrationUpPath, $columnUpSQL)) {
        $return = true;
      }
    }

    foreach ($filesDown as $migrationDown) {
      $migrationDownPath = $migrationsDownDir . DS . $migrationDown;
      if (str_contains($migrationDownPath, $columnDownSQL)) {
        $return = true;
      }
    }

    return $return;
  }
}
