<?php

namespace App\Controller\Admin;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/admin/book', name: 'admin_books')]
class BookController extends AbstractController
{
    #[Route('/', name: '_index')]
    public function index(BookRepository $bookRepository): Response
    {
        return $this->render('admin/book/index.html.twig', [
            'controller_name' => 'BookController',
            "books" => $bookRepository->findAll(),
        ]);
    }

    #[Route('/new', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $book = new Book();

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($book);
            $entityManager->flush();

            return $this->redirectToRoute('admin_books_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/book/new.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }
    #[Route('/{id}', name: '_show')]
    public function show(Book $book): Response
    {
        $form = $this->createForm(BookType::class,$book,["disabled"=>true]);
        return $this->renderForm('admin/Book/show.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Book $book): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_books_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/Book/edit.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: '_delete', methods: ['POST'])]
    public function delete(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository(Book::class)->findOneBy(["id"=>$request->get("id")]);
        if ($book != null) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($book);
            $entityManager->flush();
            return $this->json("Deleted Successfully");
        }
        return $this->json("Error",404);
    }
}
