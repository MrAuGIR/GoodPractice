<?php
namespace App;

class Render{


    public static function render(string $path, array $var){
        $loader = new \Twig\Loader\FilesystemLoader('../Templates');
        $twig = new \Twig\Environment($loader);

        echo $twig->render($path.'.html.twig', $var);
    }
}