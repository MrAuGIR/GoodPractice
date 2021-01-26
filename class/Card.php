<?php

class Card{

    private $_title;
    private $_text;
    private $_href;
    private $_imgSrc;

    public function __construct(Article $article)
    {
        $this->setTitle($article->getTitle());
        $this->setText($article->getDescription());
        $this->setHref('content/read.php?q='.$article->getId_article());
        $this->setImgSrc($article->getUrl_img());
    }


    public function getTitle():string{
        return $this->_title;
    }

    public function getText():string{
        return $this->_text;
    }

    public function getHref():string{
        return $this->_href;
    }

    public function getImgSrc():string{
        return $this->_imgSrc;
    }


    //setter

    public function setTitle($title){
         $this->_title = $title;
    }

    public function setText($text){
        $this->_text = $text;
    }

    public function setHref($href){
        $this->_href = $href;
    }

    public function setImgSrc($imgSrc){
        $this->_imgSrc = $imgSrc;
    }

    //method generate html
    /**
     * @return string $html
     */
    public function generateCard():string{

        $html ="";
        $html .= '<div class="card">';
        $html .= '<div class="picture">';
        $html .= '<img src="'.$this->getImgSrc().'" class="card-img-top" alt="illustration">';
        $html .= '</div>';
        $html .= "<div class='card-body'>";
        $html .= "<h5 class='card-title'>{$this->getTitle()}</h5>";
        $html .= "<p class='card-text'>".substr($this->getText(),0,245)."...</p>";
        $html .= "<a href='{$this->getHref()}' class='btn btn-primary'>Lire la suite</a>";
        $html .= "</div></div>";
        return $html;
    }



}

                
?>