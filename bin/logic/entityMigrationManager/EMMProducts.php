<?php

namespace Bin\Logic\EntityMigrationManager;

use App\Entities\Products;


class EMMProducts extends Products{
    

    public function getIdColumnSetup(){
        return $this->idColumnSetup;
    }

    public function getTitleColumnSetup(){
        return $this->titleColumnSetup;
    }

    public function getUserIdColumnSetup(){
        return $this->userIdColumnSetup;
    }
}