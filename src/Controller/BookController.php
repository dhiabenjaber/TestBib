<?php

namespace App\Controller;
use Symfony\Component\Security\Core\Security;
use App\Repository\CategoryRepository;
use App\Repository\BookRepository;
use App\Repository\BorrowingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Borrowing;
use App\Constants\Constants;

//Borrowing

#[Route('/book', name: 'user_book')]
class BookController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
       $this->security = $security;
    }
    #[Route('/{id}', name: '_index',methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository,BookRepository $bookRepository,BorrowingRepository $borrowingRepository,$id): Response
    {
        $user = $this->security->getUser(); 
        if(isset($user)){
            $borrowDetails = $borrowingRepository->findOneBy(['user'=>$user->getId(),'book'=>$id],['requestDateTime' => 'DESC']);
            $weeklyBorrowings = $borrowingRepository->userWeeklyUnreturned($user->getId());
            $weeklyRequests = $borrowingRepository->userWeeklyRequests($user->getId());
            $borrowingHistory = $borrowingRepository->findBy(['user'=>$user->getId(),'book'=>$id],['requestDateTime' => 'DESC','borrowingDateTime'=>'DESC','returnDateTime'=>'DESC']);
            
            $weeklyBorrows = count($weeklyBorrowings) +count($weeklyRequests); 
        }else{
            $borrowDetails=null;
            $weeklyBorrowings=[];
            $weeklyRequests=[];
            $borrowingHistory=[];
            $weeklyBorrows=0;

        }
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
            'borrowDetails'=>$borrowDetails,
            'book'=>$bookRepository->findOneBy(['id'=>$id]),
            "categories" => $categoryRepository->findAll(),
            "borrowingHistory"=>$borrowingHistory,
            "weeklyBorrowings" => $weeklyBorrowings,
            "weeklyBorrows" => $weeklyBorrows,
            "weeklyRequests"=>$weeklyRequests,
        ]);
    }


    #[Route('/{id}', name: 'borrow_book',methods: ['POST'])]
    public function borrowBook(CategoryRepository $categoryRepository,BookRepository $bookRepository,BorrowingRepository $borrowingRepository,$id): Response
    {
        $user = $this->security->getUser(); 
        $book = $bookRepository->findOneBy(['id'=>$id]);
        $borrow = new Borrowing();
        $borrow->setUser($user)
                ->setBook($book)
                ->setRequestDateTime(new \DateTime())
                ->setStatus(Constants::REQUESTED);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($borrow);
        $entityManager->flush();
        return $this->redirect("/book/{$id}");
        /*if(isset($user)){
            $borrowDetails = $borrowingRepository->findOneBy(['user'=>$user->getId(),'book'=>$id],['requestDateTime' => 'DESC']);
            $weeklyBorrowings = $borrowingRepository->userWeeklyUnreturned($user->getId());
            $weeklyRequests = $borrowingRepository->userWeeklyRequests($user->getId());
            $borrowingHistory = $borrowingRepository->findBy(['user'=>$user->getId(),'book'=>$id]);
            
        }else{
            $borrowDetails=null;
            $weeklyBorrowings=[];
            $weeklyRequests=[];
            $borrowingHistory=[];

        }

        $weeklyBorrows = count($weeklyBorrowings) +count($weeklyRequests); 
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
            'book'=>$bookRepository->findOneBy(['id'=>$id]),
            'borrowed'=>$user->getFirstName(),
            "categories" => $categoryRepository->findAll(),
            "borrowingHistory"=>$borrowingHistory,
            "weeklyBorrowings" => $weeklyBorrowings,
            "weeklyBorrows" => $weeklyBorrows,
            "weeklyRequests"=>$weeklyRequests,
        ]);*/
    }
    
}
