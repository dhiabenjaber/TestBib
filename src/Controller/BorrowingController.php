<?php

namespace App\Controller;
use Symfony\Component\Security\Core\Security;
use App\Entity\Borrowing;
use App\Repository\CategoryRepository;
use App\Repository\BookRepository;
use App\Repository\BorrowingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/borrowing', name: 'user_borrowings')]
class BorrowingController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
       $this->security = $security;
    }
    
    #[Route('/', name: '_index')]
    public function index(CategoryRepository $categoryRepository,BookRepository $bookRepository,BorrowingRepository $borrowingRepository): Response
    {
        $user = $this->security->getUser(); 
        if(isset($user)){
            $weeklyBorrowings = $borrowingRepository->userWeeklyUnreturned($user->getId());
            $weeklyRequests = $borrowingRepository->userWeeklyRequests($user->getId());
            $borrowingHistory = $borrowingRepository->findBy(['user'=>$user->getId()],['requestDateTime' => 'DESC','borrowingDateTime'=>'DESC','returnDateTime'=>'DESC']);
        }else{
            $weeklyBorrowings=[];
            $weeklyRequests=[];
            $borrowingHistory=[];
        }
        return $this->render('borrowing/index.html.twig', [
            'controller_name' => 'BorrowingController',
            "categories" => $categoryRepository->findAll(),
            "borrowings"=>$borrowingHistory,
            "weeklyBorrowings" => $weeklyBorrowings,
            "weeklyRequests"=>$weeklyRequests,
        ]);
    }

    #[Route('/{id}/delete', name: '_delete', methods: ['POST'])]
    public function delete(Request $request): Response{
        $user = $this->security->getUser(); 
        $em = $this->getDoctrine()->getManager();
        $borrowing = $em->getRepository(Borrowing::class)->findOneBy(["id" => $request->get("id"),"user"=>$user->getId()]);
        if ($borrowing != null) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($borrowing);
            $entityManager->flush();
            return $this->json("Deleted Successfully");
        }
        return $this->json("Error", 404);
    }

}
