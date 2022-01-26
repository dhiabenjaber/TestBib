<?php

namespace App\Controller\Admin;

use App\Entity\Book;
use App\Entity\Category;
use App\Entity\Image;
use App\Form\EditBorrowingType;
use App\Form\CategoryType;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/admin/category', name: 'admin_categories')]
class CategoryController extends AbstractController
{
    #[Route('/', name: '_index')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('admin/category/index.html.twig', [
            'controller_name' => 'CategoryController',
            "categories" => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('admin_categories_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }
    #[Route('/{id}', name: '_show')]
    public function show(Category $category): Response
    {
        $form = $this->createForm(CategoryType::class,$category,["disabled"=>true]);
        return $this->renderForm('admin/category/show.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_categories_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: '_delete', methods: ['POST'])]
    public function delete(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository(Category::class)->findOneBy(["id"=>$request->get("id")]);
        if ($category != null) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($category);
            $entityManager->flush();
            return $this->json("Deleted Successfully");
        }
        return $this->json("Error",404);
    }
}
