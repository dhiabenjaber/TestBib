<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use App\Form\EditUserType;
use App\Repository\CategoryRepository;
use App\Repository\BookRepository;
use App\Repository\BorrowingRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profile', name: 'user_profile')]

class ProfileController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
       $this->security = $security;
    }


    #[Route('/', name: '_index')]
    public function index(Request $request,CategoryRepository $categoryRepository,BookRepository $bookRepository,BorrowingRepository $borrowingRepository): Response
    {
        $user = $this->security->getUser();
        if(isset($user)){
            $weeklyBorrowings = $borrowingRepository->userWeeklyUnreturned($user->getId());
            $weeklyRequests = $borrowingRepository->userWeeklyRequests($user->getId());            
            $weeklyBorrows = count($weeklyBorrowings) +count($weeklyRequests); 
        }else{
            $weeklyBorrowings=[];
            $weeklyRequests=[];
            $weeklyBorrows=0;

        }
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user,
            'form' => $form->createView(),
            "categories" => $categoryRepository->findAll(),
            "weeklyBorrowings" => $weeklyBorrowings,
            "weeklyBorrows" => $weeklyBorrows,
            "weeklyRequests"=>$weeklyRequests,
        ]);
    }
}
