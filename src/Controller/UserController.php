<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\History;

use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Repository\UserRepository;

class UserController extends AbstractController
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[Route('/user', name: 'user')]
    public function index(): Response
    {


        // $entityManager = $doctrine->getManager();
        // $users = $entityManager->getRepository(User::class)->findAll();
        $users = $this->userRepository->getUsers();
        // dd($users);

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'users' => $users
        ]);
    }

    #[Route('/adduser', name: 'user_register')]
    public function userRegister(Request $request, EntityManagerInterface $em, ManagerRegistry $doctrine): Response
    {
        $user = new User();
        $user->setEmail("");
        $user->setFirstName('');
        $user->setLastName('');
        $user->setPhoneNumber('');
        $user->setAddress('');
        $user->setRoles(['ROLE_USER']);
        $form = $this->createForm(RegistrationFormType::class, $user);

        $form = $this->createFormBuilder($user)
            ->add('email', EmailType::class, ["attr" => ["class" => "form-control form-control-lg border-yellow my-2 px-5 input"]])
            ->add('first_name', TextType::class, ["attr" => ["class" => "form-control form-control-lg border-yellow my-2 px-5 input"]])
            ->add('last_name', TextType::class, ["attr" => ["class" => "form-control form-control-lg border-yellow my-2 px-5 input"]])
            ->add('phone_number', TextType::class, ["attr" => ["class" => "form-control form-control-lg border-yellow my-2 px-5 input"]])
            ->add('address', TextareaType::class, ["attr" => ["class" => "form-control form-control-lg border-yellow my-2 px-5 lowrad"]])
            ->add('save', SubmitType::class, ["label" => "Envoyer", "attr" => ["class" => "btn btn-green py-2 px-5 font-larger"]])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $datas = $form->getData();

            $em = $doctrine->getManager();
            $em->persist($datas);
            $em->flush();
            $this->addFlash('success', 'L\'utilisateur a bien été ajouté !');


            return $this->redirectToRoute('user');
        }

        return $this->render('user/addUser.html.twig', [
            'registrationForm'  => $form->createView(),
        ]);
    }

    #[Route('user/edit/{id}', name: 'user_edit')]
    public function edit(ManagerRegistry $doctrine, int $id, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);

        $form = $this->createFormBuilder($user)
            ->add('email', EmailType::class, ["attr" => ["class" => "form-control"]])
            ->add('first_name', TextType::class, ["attr" => ["class" => "form-control"]])
            ->add('last_name', TextType::class, ["attr" => ["class" => "form-control"]])
            ->add('phone_number', TextType::class, ["attr" => ["class" => "form-control"]])
            ->add('address', TextType::class, ["attr" => ["class" => "form-control"]])
            ->add('save', SubmitType::class, ["label" => "Envoyer", "attr" => ["class" => "btn btn-green"]])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', "L'utilisateur a bien été modifié");

            $edit = $form->getData();

            $user->setEmail($edit->getEmail());
            $user->setFirstName($edit->getFirstName());
            $user->setLastName($edit->getLastName());
            $user->setPhoneNumber($edit->getPhoneNumber());
            $user->setAddress($edit->getAddress());


            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('user');
        }

        return $this->renderForm('user/edit.html.twig', [
            'form' => $form,
            'id' => $id
        ]);
    }


    #[Route('user/delete/{id}', name: 'user_delete')]
    public function delete(Int $id, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $user = $em->getRepository(User::class)->find($id);
        // $history = $em->getRepository(History::class)->findOneBy(["user"=>$id]);

        // if(!is_null($history)){
        //     $user->removeUserHistory($history);
        //     $em->flush();
        //     $em->remove($user);
        //     $em->flush();
        // } else {
        $em->remove($user);
        $em->flush();
        // }
        $this->addFlash('success', "L'utilisateur' a bien été supprimé");

        return $this->redirectToRoute('user');
    }



    #[Route('user/detail/{id}', name: 'user_detail')]
    public function viewDetail(Int $id, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $user = $em->getRepository(User::class)->find($id);


        return $this->render('user/detail.html.twig', [
            'controller_name' => 'UserController',
            'user' => $user,
            'id' => $id,
        ]);
    }
}