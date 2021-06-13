<?php
namespace App;

class Render{


    public static function render(string $path, array $var){
        //$loader = new \Twig\Loader\FilesystemLoader('/homepages/27/d812845851/htdocs/projets/GoodPractice/templates');
        $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__).'/templates');
        $twig = new \Twig\Environment($loader);

        echo $twig->render($path.'.html.twig', $var);
    }

}