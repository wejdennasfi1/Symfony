<?php

namespace App\Controller;


use App\Repository\BookRepository;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Book;
use App\Form\BookType;
use App\Form\RechercheidType;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    #[Route('/showbook', name: 'showbook')]
    public function showbook(BookRepository $repositery): Response
    {

        
        $books=$repositery->findAll();
        $authorname=[];
        
        foreach($books as $book){
        $authorname[]=$book->getAuthor()->getUsername();
        
        }
        return $this->render('book/showbook.html.twig', [
            'book' => $books,
            'authorname'=>$authorname
        ]);
    }

    #[Route('/deletebook/{id}', name: 'deletebook')]
    public function deletebook(ManagerRegistry $managerRegistry,$id,BookRepository $repositery): Response
    {
        $x=$managerRegistry->getManager();
        $book=$repositery->find($id);
        $x->remove($book);
        $x->flush();
        return $this->redirectToRoute('showbook');
    }

    #[Route('/addbook', name: 'addbook')]
    public function addbook(ManagerRegistry $managerRegistry,Request $req): Response
    {
        $x=$managerRegistry->getManager();
        $book=new Book();
        $form=$this->createForm(BookType::class,$book);
        $form->handleRequest($req);
        if($form->isSubmitted() and $form->isValid()){
            $book->setEnabled(true);
            $author=$book->getAuthor();
            $author->setNbBooks($author->getNbBooks() + 1);
            $x->persist($author);
            $x->persist($book);
            $x->flush();
            return $this->redirectToRoute('showbook');
        }
        return $this->renderForm('book/addbook.html.twig', [
            'f' => $form
        ]);
    }

    #[Route('/editbook/{id}', name: 'editbook')]
    public function editbook(ManagerRegistry $managerRegistry,BookRepository $repositery,Request $req,$id): Response
    {
        $x=$managerRegistry->getManager();
        $book=$repositery->find($id);
        $form=$this->createForm(BookType::class,$book);
        $form->handleRequest($req);
        if($form->isSubmitted() and $form->isValid()){
            $x->persist($book);
            $x->flush();
            return $this->redirectToRoute('showbook');
        }

        return $this->renderForm('book/editbook.html.twig', [
            'f' => $form
        ]);
    }

    #[Route('/showpub', name: 'showpub')]
    public function showpub(BookRepository $repositery): Response
    {
        $Pubbook=[];
        $books=$repositery->findAll();
        $authorname=[];
        foreach($books as $book){
        $authorname[]=$book->getAuthor()->getUsername();
        
        }
        foreach($books as $book){
            if($book->isEnabled()==true){
                $Pubbook[]=$book;
            }
        }
        return $this->render('book/showpub.html.twig', [
            'published' => $Pubbook,
            'authorname'=>$authorname
        ]);
    }
    #[Route('/show/{id}', name: 'show')]
    public function show(BookRepository $repositery,AuthorRepository $repositery2,$id): Response
    {
        $book=$repositery->findAll();
        $author=null;
        foreach($book as $b ){
            if($b-> getId()==$id){
                $author=$b;
            }
        }

        $authornb=$author->getAuthor()->getNbBooks();
        
        return $this->render('book/show.html.twig', [
            'book' => $author,
            'nb'=>$authornb
        ]);
    }

    /****************************************************************** */

    #[Route('/searchbook', name: 'searchbook')]
    public function searchbook(BookRepository $bookRepository,Request $request): Response
    {

        $form = $this->createForm(RechercheidType::class);
        $form->handleRequest($request);
        $book=$bookRepository->findAll();
        $authorname=[];
        foreach($book as $books){
        $authorname[]=$books->getAuthor()->getUsername();}
        if ($form->isSubmitted() && $form->isValid()) {
            $id = $form['id']->getData();
            $book = $bookRepository->recherchebyref($id);
            foreach($book as $books){
                $authorname[]=$books->getAuthor()->getUsername();}
            return $this->render('book/showbook.html.twig', [
                'book' => $book,
                'authorname'=>$authorname,
            
            ]);
           // print("done");
        }
        
        return $this->renderForm('book/searchbook.html.twig', [
            'f' => $form,
            "book" =>$book,
            'authorname'=>$authorname
        ]);
    }


    #[Route('/triauthorbook', name: 'triauthorbook')]
    public function triauthorbook(BookRepository $bookRepository): Response
    {
        $authorname=[];
        $book=$bookRepository->triauthorbook();
        foreach($book as $books){
            $authorname[]=$books->getAuthor()->getUsername();}
        return $this->render('book/triauthorbook.html.twig', [
            'book' => $book,
            'authorname'=>$authorname
        ]);
    }

    #[Route('/listebook4', name: 'listebook4')]
    public function listebook4(BookRepository $bookRepository): Response
    {
        $book=$bookRepository->listebook4();
        $authorname=[];
        foreach($book as $books){
            $authorname[]=$books->getAuthor()->getUsername();}
        return $this->render('book/listebook4.html.twig', [
            'book' => $book,
            'authorname'=>$authorname
        ]);
    }

    #[Route('/changecategory', name: 'changecategory')]
    public function changecategory(BookRepository $bookRepository,ManagerRegistry $managerRegistry): Response
    {
        $x=$managerRegistry->getManager();
        $book=$bookRepository->changecategory();
        $authorname=[];
        foreach($book as $books){
            $books->setCategory('ROMANCE');
            $x->persist($books);
            $authorname[]=$books->getAuthor()->getUsername();
           
        }
        $x->flush();
        
        return $this->render('book/changecategory.html.twig', [
            'authorname'=>$authorname,
            'book' =>$book
        ]);
    }


    #[Route('/affichecategory', name: 'affichecategory')]
    public function affichecategory(BookRepository $bookRepository): Response
    {
        $book=$bookRepository->affichecategory();
        $authorname=[];
         $nbrp=0;
         $nbrN=0;
        foreach($book as $books){
            $authorname[]=$books->getAuthor()->getUsername();
            if($books->isEnabled()==1){

                $nbrp=$nbrp+1;
            }
            else {
                $nbrN=$nbrN+1;
            }
        }
        return $this->render('book/affichecategory.html.twig', [
            'book' => $book,
            'authorname'=>$authorname,
            'nbrp'=>$nbrp,
            'nbrn'=>$nbrN
        ]);
    }

    #[Route('/affiche2dates', name: 'affiche2dates')]
    public function affiche2dates(BookRepository $bookRepository): Response
    {
        $book=$bookRepository->affiche2dates();
        $authorname=[];
        foreach($book as $books){
            $authorname[]=$books->getAuthor()->getUsername();
           
        }
        return $this->render('book/affiche2dates.html.twig', [
            'book' => $book,
            'authorname'=>$authorname,
        ]);
    }
   
}
