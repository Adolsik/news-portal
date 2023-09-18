<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\NewsRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(NewsRepository $repo, CategoryRepository $catRepo): Response
    {
        $news = $repo->findAll();
        $listOfCategories = $catRepo->findAll();


        return $this->render('main/index.html.twig',[
            'news' =>$news,
            'category' =>$listOfCategories
        ]);
    }

    #[Route('category/{id}', name: 'home_category')]
    public function indexCategory($id, NewsRepository $repo, CategoryRepository $catRepo,){

        $listOfCategories = $catRepo->findAll();
        $news = $repo->FindByCategory($id);

        return $this->render('main/index.html.twig',[
            'news' =>$news,
            'category' =>$listOfCategories
        ]);
    }
}
