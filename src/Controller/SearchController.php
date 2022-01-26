<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use App\Repository\AuthorRepository;
use App\Repository\EditorRepository;
use App\Repository\BookRepository;
use App\Repository\BorrowingRepository;
use Symfony\Component\Security\Core\Security;


#[Route('/search', name: 'books_search')]
class SearchController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
       $this->security = $security;
    }


    #[Route('/', name: '_index')]
    public function index(Request $request,CategoryRepository $categoryRepository,BookRepository $bookRepository,BorrowingRepository $borrowingRepository,AuthorRepository $authorRepository,EditorRepository $editorRepository): Response
    {
        $user = $this->security->getUser();
        $keyword = $request->query->get('keyword');

        $res = $this->search($request,$bookRepository);
        $js = json_decode($res->getContent(),true);
        
        if(isset($user)){
            $weeklyBorrowings = $borrowingRepository->userWeeklyUnreturned($user->getId());
            $weeklyRequests = $borrowingRepository->userWeeklyRequests($user->getId());            
            $weeklyBorrows = count($weeklyBorrowings) +count($weeklyRequests); 
        }else{
            $weeklyBorrowings=[];
            $weeklyRequests=[];
            $weeklyBorrows=0;
        }

        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
            "categories" => $categoryRepository->findAll(),
            "authors"=>$authorRepository->findAll(),
            "editors"=>$editorRepository->findAll(),
            "keyword"=>$keyword,
            "result"=>$js,
            "weeklyBorrowings" => $weeklyBorrowings,
            "weeklyBorrows" => $weeklyBorrows,
            "weeklyRequests"=>$weeklyRequests,
        ]);
    }

    #[Route('/find', name: '_search')]
    public function search(Request $request,BookRepository $bookRepository): Response{
        $keyword = $request->query->get('keyword');
        $pmax = $request->query->get('priceMax');
        $categories = $request->query->get('categories');
        $authors = $request->query->get('authors');
        $editors = $request->query->get('editors');

        $res = $bookRepository->searchBooks($keyword,$pmax,$categories,$authors,$editors);
        return $this->json($res);
    }
}
