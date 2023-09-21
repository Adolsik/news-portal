<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\News;
use App\Form\CommentsType;
use App\Form\NewsType;
use App\Repository\CommentsRepository;
use App\Repository\NewsRepository;
use App\Service\CommentsGenerator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Path;
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
    public function show($id, NewsRepository $repo, CommentsRepository $comRepo, Request $request, ManagerRegistry $doctrine, CommentsGenerator $commentsGenerator ){
        $news = $repo->find($id);
        $allNews = $repo->FindLatestNews();

        $allComments = $comRepo->getNewsComments($id);
   
        #Comments form handling
        $comment = new Comments();
        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);
        
        if($form->isSubmitted()){
            $form = $commentsGenerator->handleComments($comment, $form, $news, $this->getUser(), $doctrine);
            
            return $this->redirect($request->getUri());
        }

        return $this->render('news/show.html.twig',[
            'news' => $news,
            'allNews' =>$allNews,
            'form' => $form->createView(),
            'comments' => $allComments
        ]);
    }

    #[Route(path: '/delete/{id}', name: 'delete')]
    public function delete($id, NewsRepository $news, ManagerRegistry $doctrine)
    {
        $manager = $doctrine->getManager();
        $news->DeleteNews($id);

        $manager->flush();

        return $this->redirectToRoute('home');
    }   
}
