<?php
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

use App\Models\Card;
use App\Models\Article;
use App\Managers\ManagerArticle;

if(isset($_GET['categorySearch']) && $_GET['categorySearch'] === "1" ){

    $tab = [];
    $content = [];

    $categoryId = (!empty($_GET['category'])) ? (int)$_GET['category'] : null;
    
    $managerArticle = new ManagerArticle();
    $articles = $managerArticle->getArticleByFilter($categoryId);
    $html = "";
    foreach($articles as $article){
        /** @var Article $article */
        $card = new Card($article);
        /** @var Card $card */
        // $tab['href'] = $card->getHref();
        // $tab['img'] = $card->getImgSrc();
        // $tab['title'] = $card->getTitle();
        // $tab['text'] = $card->getText();
        // $content[] = json_encode($tab);
        $html .= $card->generateCard(2);
    }

    echo json_encode(['content'=>$html],JSON_HEX_TAG);

}

?>