<?php

namespace Core;

use \Twig\Environment;
use \Twig\Lexer;
use \Twig\Loader\FilesystemLoader;

class Twig
{
    private $_loader, $_twig;
    public function __construct()
    {
        $this->_loader = new FilesystemLoader(ROOT . DS . "app" . DS . "views");
        $this->_twig  = new Environment($this->_loader, []);
    }

    public function templateFileSystem($path)
    {
        $this->_loader = new FilesystemLoader($path);
        $this->_twig = new Environment($this->_loader);
    }

    public function setLexer($block = null, $variable = null)
    {
        if (!isset($block)) {
            $block = ["{", "}"];
        }
        if (!isset($variable)) {
            $variable = ["{{", "}}"];
        }
        $lexer = new Lexer($this->_twig, [
            "tag_block" => $block,
            "tag_variable" => $variable
        ]);
        $this->_twig->setLexer($lexer);
    }

    public function render($template, $data = [])
    {
        $temp = $this->_twig->load($template);
        echo $temp->render($data);
    }

    public function getTwig()
    {
        return $this->_twig;
    }
}
