<?php

namespace App\Controller;

use App\Entity\News;
use App\Repository\NewsRepository;
use DateTimeInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
        $form = $this->createFormBuilder()
        ->add('title', TextType::class)
        ->add('content', TextareaType::class)
        ->add('thumbnail', FileType::class)
        ->add('submit', SubmitType::class, [
            'label' => 'Create'
        ])
        ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted()){
            $manager = $doctrine->getManager();

            $form_data = $form->getData();

            $news = new News();
            $news->setTitle($form_data['title']);
            $news->setContent($form_data['content']);

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
