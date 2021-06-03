<?php
namespace App;

class Render{


    public static function render(string $path, array $var){
        $loader = new \Twig\Loader\FilesystemLoader('/homepages/27/d812845851/htdocs/projets/GoodPractice/templates');
        $twig = new \Twig\Environment($loader);

        echo $twig->render($path.'.html.twig', $var);
    }

}