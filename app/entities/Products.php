<?php

namespace App\Entities;

class Products{
    

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

    

    private $title;
    public $titleColumnSetup = [
        "type"=>"string",
        "length"=>255,
        "nullable"=>0
    ];
    
    public function getTitle(){
        return $this->title;
    }

    public function setTitle($title){
        $this->title = $title;
    }

    private $userId;
    public $userIdColumnSetup = [
        "relation"=>"user",
        "type"=>"int",
        "constraint"=>"userId",
        "length"=>11,
        "nullable"=>0
    ];
    
    public function getUserId(){
        return $this->userId;
    }

    public function setUserId($userId){
        $this->userId = $userId;
    }
}