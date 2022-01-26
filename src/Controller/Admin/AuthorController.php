<?php

namespace App\Controller\Admin;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/author', name: 'admin_authors')]
class AuthorController extends AbstractController
{
    #[Route('/', name: '_index')]
    public function index(AuthorRepository $authorRepository): Response
    {
        return $this->render('admin/author/index.html.twig', [
            'controller_name' => 'AuthorController',
            "authors" => $authorRepository->findAll(),
        ]);
    }


    #[Route('/new', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($author);
            $entityManager->flush();

            return $this->redirectToRoute('admin_authors_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/author/new.html.twig', [
            'author' => $author,
            'form' => $form,
        ]);
    }
    #[Route('/{id}', name: '_show')]
    public function show(Author $author): Response
    {
        $form = $this->createForm(AuthorType::class,$author,["disabled"=>true]);
        return $this->renderForm('admin/author/show.html.twig', [
        'author' => $author,
        'form' => $form,
    ]);
    }

    #[Route('/{id}/edit', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Author $author): Response
    {
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_authors_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/author/edit.html.twig', [
            'author' => $author,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: '_delete', methods: ['POST'])]
    public function delete(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $author = $em->getRepository(Author::class)->findOneBy(["id"=>$request->get("id")]);
        if ($author != null) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($author);
            $entityManager->flush();
            return $this->json("Deleted Successfully");
        }
        return $this->json("Error",404);
    }

}
