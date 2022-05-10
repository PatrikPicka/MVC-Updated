<?php
namespace Bin\Logic\CreateMigration;
                
class GetMigrationData
    {
        private $_columnsArray = [];
        public function __construct()
        {

    $Products = new \Bin\Logic\EntityMigrationManager\EMMProducts;

        $this->_columnsArray["products"]["id"] = $Products->getIdColumnSetup();

        $this->_columnsArray["products"]["title"] = $Products->getTitleColumnSetup();

        $this->_columnsArray["products"]["userId"] = $Products->getUserIdColumnSetup();

    $User = new \Bin\Logic\EntityMigrationManager\EMMUser;

        $this->_columnsArray["user"]["id"] = $User->getIdColumnSetup();

        $this->_columnsArray["user"]["username"] = $User->getUsernameColumnSetup();

        $this->_columnsArray["user"]["password"] = $User->getPasswordColumnSetup();

        $this->_columnsArray["user"]["birtDate"] = $User->getBirtDateColumnSetup();
        return $this->_columnsArray;
    }
}