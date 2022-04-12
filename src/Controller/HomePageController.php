<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Books;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Entity\History;
use App\Form\BookFormType;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Doctrine\ORM\EntityManagerInterface;


class HomePageController extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function index(ManagerRegistry $doctrine, UserRepository $userRepository): Response
    {
        //Modifier la requête book pour qu'elle me ramène l'history et les users
        $entityManager = $doctrine->getManager();
        $books = $entityManager->getRepository(Books::class)->findAll();
        $users = $userRepository->getAllUsers();


        return $this->render('home_page/index.html.twig', [
            'controller_name' => 'HomePageController',
            'books' => $books,
            'users' => $users,
        ]);
    }


    #[Route('/home/create', name: 'book_create')]
    public function create(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $book = new Books();
        $form = $this->createForm(BookFormType::class, $book);
        //->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book = $form->getData();
            $bookCover = $form->get('cover')->getData();

            if ($bookCover) {
                $originalFilename = pathinfo($bookCover->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = '/library/book_cover/' . $safeFilename . '-' . uniqid() . '.' . $bookCover->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $bookCover->move(
                        $this->getParameter('book_cover_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $book->setImageName($newFilename);
            }

            // $book = $form->getData();
            $book->setDate(new \DateTime("now"));

            // $em ->getManager();
            $em->persist($book);
            $em->flush();
            $this->addFlash('success', 'Votre livre a bien été ajouté');


            return $this->redirectToRoute('home');
        }

        return $this->renderForm('home_page/create.html.twig', [
            'form' => $form,
        ]);
    }



    #[Route('/home/edit/{id}', name: 'book_edit')]
    public function edit(ManagerRegistry $doctrine, int $id, Request $request, SluggerInterface $slugger): Response
    {
        $entityManager = $doctrine->getManager();
        $book = $entityManager->getRepository(Books::class)->find($id);

        $form = $this->createForm(BookFormType::class, $book);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $edit = $form->getData();
            $bookCover = $form->get('cover')->getData();

            if ($bookCover) {
                $originalFilename = pathinfo($bookCover->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = '/library/book_cover/' . $safeFilename . '-' . uniqid() . '.' . $bookCover->guessExtension();

                try {
                    $bookCover->move(
                        $this->getParameter('book_cover_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $book->setImageName($newFilename);
            }

            // $book->setTitle($edit->getTitle());
            // $book->setAuthor($edit->getAuthor());
            // $book->setDescription($edit->getDescription());
            // $book->setPublisher($edit->getPublisher());
            // $book->setCategory($edit->getCategory());

            $entityManager->persist($book);
            $entityManager->flush();
            $this->addFlash('success', 'Le livre a bien été modifié');

            return $this->redirectToRoute('home');
        }

        return $this->renderForm('home_page/edit.html.twig', [
            'form' => $form,
            'id' => $id,
            'status' => $book->getStatus()
        ]);
    }

    #[Route('/home/detail/{id}', name: 'book_detail')]
    public function viewBookDetail(Int $id, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $book = $em->getRepository(Books::class)->find($id);
        return $this->render('home_page/detail.html.twig', [
            'book' => $book,
            'id' => $id,
        ]);
    }

    #[Route('/home/delete/{id}', name: 'delete_book')]
    public function delete(Int $id, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $book = $em->getRepository(Books::class)->find($id);
        //$history = $em->getRepository(History::class)->findOneBy(["book" => $id]);

        // if (!is_null($history)) {
        //     $book->removeBookHistory($history);
        // }

        $em->remove($book);
        $em->flush();

        $this->addFlash('success', 'Le livre a bien été supprimé');

        return $this->redirectToRoute('home');
    }

    #[Route('/home/borrow', name: 'book_borrow', methods: ["GET"])]
    public function borrowBook(ManagerRegistry $doctrine): Response
    {
        // dump($_GET["bookId"]);
        $entityManager = $doctrine->getManager();
        $book = $entityManager->getRepository(Books::class)->find($_GET["bookId"]);
        $user = $entityManager->getRepository(User::class)->find($_GET["userId"]);

        $history = new History();
        $history->setUser($user);
        $history->setBook($book);
        $history->setLoanDate(new \DateTime("now"));
        $history->setDueDate(new \DateTime('+30days'));
        $book->setStatus(Books::EMPRUNTE);

        $entityManager->persist($history);
        $entityManager->flush();

        $this->addFlash('success', 'Le livre a bien été emprunté');
        return $this->redirectToRoute('home');
    }

    #[Route('/home/return/{id}', name: 'book_return')]
    public function return(ManagerRegistry $doctrine, int $id): Response
    {
        // $entityManager = $doctrine->getManager();
        // $book = $entityManager->getRepository(Books::class)->find($id);

        // $book->setLastUser(null);
        // $book->setLoanDate(null);
        // $book->setDueDate(null);
        // $book->setStatus(Books::DISPONIBLE);


        // $entityManager->persist($book);
        // $entityManager->flush();
        $this->addFlash('success', 'Le livre a bien été rendu !');
        return $this->redirectToRoute('home');
    }
}
    
        // ->add('last_user', EntityType::class, [
        //     'class' => User::class,
        //         'query_builder' => function (EntityRepository $er) {
        //             return $er->createQueryBuilder('u')
        //             ->where('u.roles = :roles')
        //             ->setParameter('roles', '["ROLE_USER"]');
        //         },
        //         'choice_label' => 'id',
        //         'attr' => ['class' => 'form-control'],
        //         'label' => 'Utilisateur',
        //         'placeholder' => 'Aucun',
        //         'required' => false
        //         ])
                
                
        //     ->add('save', SubmitType::class, ["label" => "Envoyer", "attr" => ["class" => "btn btn-green"]])
        //     ->getForm();