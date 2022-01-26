<?php

namespace App\Controller\Admin;

use App\Entity\Editor;
use App\Form\EditorType;
use App\Repository\EditorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/admin/editor', name: 'admin_editors')]
class EditorController extends AbstractController
{
    #[Route('/', name: '_index')]
    public function index(EditorRepository $editorRepository): Response
    {
        return $this->render('admin/editor/index.html.twig', [
            'controller_name' => 'editorController',
            "editors" => $editorRepository->findAll(),
        ]);
    }

    #[Route('/new', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $editor = new Editor();

        $form = $this->createForm(EditorType::class, $editor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($editor);
            $entityManager->flush();

            return $this->redirectToRoute('admin_editors_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/editor/new.html.twig', [
            'editor' => $editor,
            'form' => $form,
        ]);
    }
    #[Route('/{id}', name: '_show')]
    public function show(Editor $editor): Response
    {
        $form = $this->createForm(EditorType::class,$editor,["disabled"=>true]);
        return $this->renderForm('admin/editor/show.html.twig', [
            'editor' => $editor,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Editor $editor): Response
    {
        $form = $this->createForm(EditorType::class, $editor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_editors_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/editor/edit.html.twig', [
            'editor' => $editor,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: '_delete', methods: ['POST'])]
    public function delete(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $editor = $em->getRepository(Editor::class)->findOneBy(["id"=>$request->get("id")]);
        if ($editor != null) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($editor);
            $entityManager->flush();
            return $this->json("Deleted Successfully");
        }
        return $this->json("Error",404);
    }
}
