<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $userPassword): Response
    {

        $form = $this->createFormBuilder()
        ->add('username')
        ->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'required' => true,
            'first_options' => ['label' => 'Password'],
            'second_options' => ['label' => 'Confirm Password'],
        ])
        ->add('email', EmailType::class, [
            'required' => true
        ])
        ->add('gender', ChoiceType::class, [
            'choices' => [
                'Male' => 'male',
                'Female' => 'female'
            ]
        ])
        ->add('image', FileType::class)
        ->add('register', SubmitType::class, [
            'attr' => [
                'class' => 'border rounded-lg p-2 border-neutral-600 mt-10'
            ]
        ])
        ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted()){
            $manager = $doctrine->getManager();

            $form_data = $form->getData();

            $user = new User();
            $user->setUsername($form_data['username']);
            $user->setEmail($form_data['email']);
            $user->setPassword(
                $userPassword->hashPassword($user, $form_data['password'])
            );
            $user->setGender($form_data['gender']);
            $user->setImage($form_data['image']);

            $manager->persist($user);
            $manager->flush();

            return $this->redirect($this->generateUrl('app_login'));
        }

        return $this->render('registration/index.html.twig', [
           'form' => $form->createView()
        ]);
    }
}
