<?php

use Bin\Logic\RegenerateEntity\RegenerateEntity;
use Core\Twig;

class DefaultController
{
    public function indexAction()
    {
        /*$twig = new Twig();

        $twig->render("index.php", ["name" => "Patrik"]);*/

        $regen = new RegenerateEntity;
        /*
        $path    = ROOT . DS . 'app' . DS . 'entities';
        $files = scandir($path);
        if ($files[0] === "." || $files[0] === "..") {
            array_shift($files);
        }
        if ($files[0] === "." || $files[0] === "..") {
            array_shift($files);
        }
        foreach ($files as $file) {
            echo str_replace(".php", "", $file);
        }*/


        /*$createMigration = new GenerateMigration();
        if ($createMigration->generateMigrations()) {
            echo "Migration created successfuly";
        } else {
            echo "There was a problem creating your migration. Probably you did not change anything from previous migration.";
        }*/
    }
}
