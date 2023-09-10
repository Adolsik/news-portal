<?php

namespace App\Controller;

use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(NewsRepository $repo): Response
    {
        $news = $repo->findAll();

        return $this->render('main/index.html.twig',[
            'news' =>$news
        ]);
    }
}
