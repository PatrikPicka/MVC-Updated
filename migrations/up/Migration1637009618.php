<?php
namespace Migrations\Up;

use Core\MigrateMigration;

class Migration1637009618 extends MigrateMigration{
    public function __construct(){
        $this->createSQL("CREATE TABLE products ( id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY );");
        
        $this->createSQL("CREATE TABLE user ( id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY );");
        
        $this->createSQL("ALTER TABLE products ADD title varchar(255) NOT NULL ;");
        
        $this->createSQL("ALTER TABLE user ADD username varchar(255) NOT NULL ;");
        
        $this->createSQL("ALTER TABLE user ADD password varchar(255) NOT NULL ;");
        
        $this->createSQL("ALTER TABLE user ADD birtDate datetime NOT NULL ;");
        
        $this->createSQL("ALTER TABLE products ADD userId int(11) NOT NULL; ALTER TABLE products ADD CONSTRAINT userId FOREIGN KEY (userId) REFERENCES user(id);");
        $this->migrate();
    }
}