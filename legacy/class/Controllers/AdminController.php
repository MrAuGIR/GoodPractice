<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Application;
use App\Db;
use App\Managers\ManagerCategory;
use App\Managers\ManagerUser;
use App\Render;

class AdminController extends Controller
{
    protected $managerName = \App\Managers\ManagerArticle::class;
    private $_category;

    public function __construct()
    {
        parent::__construct();
        $managerCategory = new ManagerCategory();
        $this->_category = $managerCategory->getAllCategory();
    }

    public function adminArticle()
    {
        //on regarde si un utilisateur est connecté
        $user = Application::secure();

        //creation du manager article

        if ($user['role'] === LEVEL_ADMIN) {
            $articles = $this->manager->getAllArticle();
        } else {
            $articles = $this->manager->getArticleByIdUser($user['id']);
        }


        Render::render('articles/admin', ['title' => 'Administration', 'user' => $user, 'articles' => $articles]);
    }

    public function adminCategory()
    {
        $user = Application::secure([LEVEL_ADMIN]);

        $categories = $this->_category;
        

        Render::render('categories/admin', ['title' => 'Administration', 'user' => $user, 'categories' => $categories]);
    }

    public function adminUser()
    {
        $user = Application::secure([LEVEL_ADMIN]);
        $managerUser = new ManagerUser();
        $users = $managerUser->getAll();

        Render::render('users/admin',['title' => 'Administration', 'user' => $user, 'users' => $users]);
    }
}