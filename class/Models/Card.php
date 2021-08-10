<?php
namespace App\Models;


class Card{

    private $_title;
    private $_text;
    private $_href;
    private $_imgSrc;
    private $_numComment;

    public function __construct(Article $article)
    {
        $this->setTitle($article->getTitle());
        $this->setText($article->getDescription());
        $this->setHref('/article/show?q='.$article->getId_article());
        $this->setImgSrc($article->getUrl_img());
        $this->setNumComment($article->getNum_commnents());
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

    public function getNumCommnents():int{
        return $this->_numComment;
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

    public function setNumComment($num){
        $this->_numComment = $num;
    }


    /**
     * method generate html
     * @param int $number Nombre de card à afficher en full screen
     * @return string $html
     */
    public function generateCard(int $number = 3):string{

        $html ="";
        if($number < 3){
            $html .= '<div class="col-12 col-sm-12 col-md-6 ">';
        }else{
            $html .= '<div class="col-12 col-sm-12 col-md-6 col-lg-4">';
        }
        $html .= "<a href='{$this->getHref()}' class='card-link'>";
        $html .= '<div class="card my-2 ">';
        $html .= '<div class="picture">';
        $html .= '<img src="/public/'.$this->getImgSrc().'" class="card-img-top" alt="illustration">';
        $html .= '</div>';
        $html .= "<div class='card-body'>";
        $html .= "<h5 class='card-title'>{$this->getTitle()}</h5>";
        
        if(strlen($this->getText()) < 200 ){
            $html .= "<div style='height:120px' class='card-text'>" . strip_tags(substr($this->getText(), 0, 200)) . "...</div>";
        }else{
            $html .= "<div class='card-text py-2'>" . strip_tags(substr($this->getText(), 0, 200)) . "...</div>";
        }
        $html .= "</div>";
        $html .= "<div class='comments-line'><i class='fas fa-comments'></i><span class='number'>" . $this->getNumCommnents() . "</span></div>";
        $html .= "</div></a></div>";
        return $html;
    }


    public function generateMainCard()
    {
        $html = "";
        $html .= '<div class="col-12">';
        $html .= "<a href='{$this->getHref()}' class='card-link'>";
        $html .= '<div class="card card-main my-2 ">';
        $html .= '<div class="picture">';
        $html .= '<img src="/public/' . $this->getImgSrc() . '" class="card-img-top" alt="illustration">';
        $html .= '</div>';
        $html .= "<div class='card-body'>";
        $html .= "<h5 class='card-title'>{$this->getTitle()}</h5>";
        
        if (strlen($this->getText()) < 200) {
            $html .= "<div style='height:120px' class='card-text'>" . strip_tags(substr($this->getText(), 0, 200)) . "...</div>";
        } else {
            $html .= "<div class='card-text py-2'>" . strip_tags(substr($this->getText(), 0, 200)) . "...</div>";
        }
        $html .= "</div></div></a></div>";
        return $html;
    }



}

                
?>