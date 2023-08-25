<?php
abstract class Base{
    protected $Utils;
    protected $DB;
    protected $templatePath;
    protected $twigLoader;
    protected $twig;
    protected $language;
    protected $lang;
    public function __construct() {
        $this->Utils = Utils::getInstance();
        
    }
    protected function initTwig($mode) { //call twig
        $this->templatePath = ROOT_PATH.'view/'.$mode.'/default/';
        $this->twigLoader = new \Twig\Loader\FilesystemLoader($this->templatePath);
        $this->twig = new \Twig\Environment($this->twigLoader);
        $filter = new \Twig\TwigFilter('lang', function ($string) {
            return $this->lang[$string]??$string;
        });
        $this->twig->addFilter($filter);
    }
    public function Render($file,$data=array()){
        $twigFile = $file.'.html';
        $filePath = $this->templatePath.$twigFile;
        if(file_exists($filePath))
        print $this->twig->render($twigFile,$data);
    }
}

?>