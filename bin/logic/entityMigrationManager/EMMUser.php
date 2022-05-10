<?php

namespace Bin\Logic\EntityMigrationManager;

use App\Entities\User;


class EMMUser extends User{
    

    public function getIdColumnSetup(){
        return $this->idColumnSetup;
    }

    public function getUsernameColumnSetup(){
        return $this->usernameColumnSetup;
    }

    public function getPasswordColumnSetup(){
        return $this->passwordColumnSetup;
    }

    public function getBirtDateColumnSetup(){
        return $this->birtDateColumnSetup;
    }
}