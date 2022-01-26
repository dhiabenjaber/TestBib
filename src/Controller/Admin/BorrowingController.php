<?php

namespace App\Controller\Admin;

use App\Constants\Constants;
use App\Entity\Borrowing;
use App\Form\BorrowingType;
use App\Form\EditBorrowingType;
use App\Repository\BorrowingRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/borrowing', name: 'admin_borrowings')]
class BorrowingController extends AbstractController
{
    #[Route('/', name: '_index')]
    public function index(BorrowingRepository $borrowingRepository): Response
    {
        return $this->render('admin/borrowing/index.html.twig', [
            'controller_name' => 'borrowingController',
            "borrowings" => $borrowingRepository->findBy([], ["borrowingDateTime" => "DESC"]),
        ]);
    }


    #[Route('/new', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $borrowing = new Borrowing();
        $borrowing->setStatus(Constants::REQUESTED);
        $form = $this->createForm(EditBorrowingType::class, $borrowing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($borrowing);
            $entityManager->flush();

            return $this->redirectToRoute('admin_borrowings_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/borrowing/new.html.twig', [
            'borrowing' => $borrowing,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/accept', name: '_accept', methods: ['GET', 'POST'])]
    public function acceptBorrowingRequest(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $borrowing = $entityManager->getRepository(Borrowing::class)->findOneBy(["id" => $request->get("id")]);
        if ($borrowing != null) {
            $oldStatus = $borrowing->getStatus();
            $borrowing->setStatus(Constants::BORROWED);
            if ($oldStatus != $borrowing->getStatus()) {
                $borrowing->getBook()->decreaseNumberOfCopies();
            }
            $borrowing->setBorrowingDateTime(new \DateTime());
            $entityManager->flush();
            return $this->json("Borrowed Successfully");
        }
        return $this->json("Error", 404);
    }

    #[Route('/{id}/decline', name: '_decline', methods: ['GET', 'POST'])]
    public function declineBorrowingRequest(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $borrowing = $entityManager->getRepository(Borrowing::class)->findOneBy(["id" => $request->get("id")]);
        if ($borrowing != null) {
            $borrowing->setStatus(Constants::DECLINED);
            $entityManager->flush();
            return $this->json("Declined Successfully");
        }
        return $this->json("Error", 404);
    }

    #[Route('/{id}', name: '_show')]
    public function show(Borrowing $borrowing): Response
    {
        $form = $this->createForm(BorrowingType::class, $borrowing, ["disabled" => true]);
        return $this->renderForm('admin/borrowing/show.html.twig', [
            'borrowing' => $borrowing,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $borrowing = null;
        try {
            $borrowing = $em->getRepository(Borrowing::class)
                ->createQueryBuilder("b")
                ->where("b.id = :id")
                ->setParameter("id", $request->get("id"))
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            return $this->render('error/404.html.twig');
        } catch (NonUniqueResultException $e) {
        }
        if ($borrowing != null) {
            $oldStatus = $borrowing->getStatus();
            $form = $this->createForm(EditBorrowingType::class, $borrowing);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $newStatus = $borrowing->getStatus();
                if ($newStatus != $oldStatus) {

                    if ($newStatus == Constants::RETURNED) {
                        $borrowing->getBook()->increaseNumberOfCopies();
                    }
                    if ($newStatus == Constants::NEVER_RETURNED && $oldStatus == Constants::RETURNED) {
                        $borrowing->getBook()->decreaseNumberOfCopies();
                    }

                }
                $em->flush();
                return $this->redirectToRoute('admin_borrowings_index', [], Response::HTTP_SEE_OTHER);
            }
            return $this->renderForm('admin/borrowing/edit.html.twig', [
                'borrowing' => $borrowing,
                'form' => $form,
            ]);
        }
        return $this->render('error/404.html.twig');
    }

    #[Route('/{id}/delete', name: '_delete', methods: ['POST'])]
    public function delete(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $borrowing = $em->getRepository(Borrowing::class)->findOneBy(["id" => $request->get("id")]);
        if ($borrowing != null) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($borrowing);
            $entityManager->flush();
            return $this->json("Deleted Successfully");
        }
        return $this->json("Error", 404);
    }

}
