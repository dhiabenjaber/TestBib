<?php

namespace App\Controller;
use App\Repository\BookRepository;
use Symfony\Component\Security\Core\Security;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\BorrowingRepository;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/', name: 'home')]
class HomeController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
       $this->security = $security;
    }
    #[Route('/', name: '_index')]
    public function index(BookRepository $bookRepository, CategoryRepository $categoryRepository,BorrowingRepository $borrowingRepository): Response
    {
        $user = $this->security->getUser(); 
        if(isset($user)){
            $weeklyBorrowings = $borrowingRepository->userWeeklyUnreturned($user->getId());
            $weeklyRequests = $borrowingRepository->userWeeklyRequests($user->getId());
        }else{
            $weeklyBorrowings=[];
            $weeklyRequests=[];
        }
        
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            "books" => $bookRepository->findAll(),
            "categories" => $categoryRepository->findAll(),
            "weeklyBorrowings" => $weeklyBorrowings,
            "weeklyRequests"=>$weeklyRequests,
        ]);
    }
}
