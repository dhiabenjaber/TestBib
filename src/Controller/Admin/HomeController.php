<?php

namespace App\Controller\Admin;

use App\Constants\Constants;
use App\Entity\Book;
use App\Entity\Borrowing;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'admin_home')]
class HomeController extends AbstractController
{

    #[Route('/', name: '_index')]
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $requestedBorrowings = $em->getRepository(Borrowing::class)->findBy(["status"=>Constants::REQUESTED]);
        $toBeReturnedBorrowings = $em->getRepository(Borrowing::class)->findBy(["status"=>Constants::BORROWED]);
        $numberOfUsers = $em->getRepository(User::class)->getNumberOfUsers();
        $numberOfRequestedBooks = $em->getRepository(Borrowing::class)->numberOfRequestedBooks();
        $numberOfUnreturnedBooks = $em->getRepository(Borrowing::class)->numberOfUnreturnedBooks([Constants::NEVER_RETURNED,Constants::BORROWED]);
        return $this->render('admin/home/home.html.twig', [
            'requestedBorrowings'=>$requestedBorrowings,
            'toBeReturnedBorrowings'=>$toBeReturnedBorrowings,
            'controller_name' => 'HomeController',
            'numberOfUsers'=>  $numberOfUsers[1],
            'numberOfRequestedBooks'=>$numberOfRequestedBooks[1],
            'numberOfUnreturnedBooks'=>$numberOfUnreturnedBooks[1],
        ]);
    }
}
