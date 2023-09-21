<?php

namespace App\Service;

use App\Entity\Comments;
use App\Entity\News;
use App\Entity\User;
use Symfony\Bridge\Doctrine\ManagerRegistry;

class CommentsGenerator{

    public function __construct()
    {
        
    }

    public function handleComments(Comments $comment, mixed $form, News $news, User $user, ManagerRegistry $doctrine){

         $manager = $doctrine->getManager();
         $comment->setNews($news);
         $comment->setUser($user);

         $content = $form['content']->getData();
         $comment->setContent($content);
         
         $comment->setDate(new \DateTime('@'.strtotime('now')));

         $manager->persist($comment);
         $manager->flush();

         return $form;

    }


}

