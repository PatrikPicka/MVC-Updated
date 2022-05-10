<?php
namespace Migrations\Down;

use Core\MigrateMigration;
        
class Migration1637009618 extends MigrateMigration{
    public function __construct(){
        $this->createSQL("ALTER TABLE products DROP FOREIGN KEY userId;");
        
        $this->createSQL("ALTER TABLE products DROP title;");
        
        $this->createSQL("ALTER TABLE user DROP username;");
        
        $this->createSQL("ALTER TABLE user DROP password;");
        
        $this->createSQL("ALTER TABLE user DROP birtDate;");
        
        $this->createSQL("DROP TABLE products;");
        
        $this->createSQL("DROP TABLE user;");
        $this->migrate();
    }
}