<?php

namespace App\Controller;

use App\Entity\News;
use App\Form\NewsType;
use App\Repository\NewsRepository;
use DateTimeInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/news", name: "news.")]
class NewsController extends AbstractController
{
    #[Route('/create', name: 'create')]
    public function create(Request $request, ManagerRegistry $doctrine, ): Response
    {
        // Create form for adding news
        $news = new News();
        $form = $this->createForm(NewsType::class, $news);

        $form->handleRequest($request);

        if($form->isSubmitted()){
            $manager = $doctrine->getManager();

            $dateNow = new \DateTime('@'.strtotime('now'));
            $news->setCreateDate($dateNow);
            
            $file = $form['thumbnail']->getData();

            if($file){
                $filename = md5(uniqid()).".".$file->guessExtension();
                $file->move(
                    $this->getParameter('upload_dir'),
                    $filename
                );
            } else {
                $filename = 'defaultThumbnail.jpg';
            }
            $news->setThumbnail($filename);

            $manager->persist($news);
            $manager->flush();

            return $this->redirect($this->generateUrl('home'));
        }

        return $this->render('news/index.html.twig', [
            "form" => $form->createView()
        ]);
    }

    #[Route(path: '/show/{id}', name: 'show')]
    public function show($id, NewsRepository $repo){
        $news = $repo->find($id);

        return $this->render('news/show.html.twig',[
            'news' => $news
        ]);
    }
}
