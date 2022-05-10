<?php

namespace App\Entities;

class User{
    

    private $id;
    public $idColumnSetup = [
        "type"=>"int",
        "length"=>11,
        "autoIncrement"=>1,
        "index"=>"primary"
    ];
    
    public function getId(){
        return $this->id;
    }

    

    private $username;
    public $usernameColumnSetup = [
        "type"=>"string",
        "length"=>255,
        "nullable"=>0
    ];
    
    public function getUsername(){
        return $this->username;
    }

    public function setUsername($username){
        $this->username = $username;
    }

    private $password;
    public $passwordColumnSetup = [
        "type"=>"string",
        "length"=>255,
        "nullable"=>0
    ];
    
    public function getPassword(){
        return $this->password;
    }

    public function setPassword($password){
        $this->password = $password;
    }

    private $birtDate;
    public $birtDateColumnSetup = [
        "type"=>"datetime",
        "length"=>"skip",
        "nullable"=>0
    ];
    
    public function getBirtDate(){
        return $this->birtDate;
    }

    public function setBirtDate($birtDate){
        $this->birtDate = $birtDate;
    }
}