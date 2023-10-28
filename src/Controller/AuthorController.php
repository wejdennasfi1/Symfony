<?php

namespace App\Controller;

use App\Repository\AuthorRepository;
use App\Entity\Author;
use App\Form\AuthorType;
use App\Form\MinmaxType;
use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    public $authors = array(
        array('id' => 1, 'picture' => '/images/Victor-Hugo.jpg','username' => 'Victor Hugo', 'email' => 'victor.hugo@gmail.com ', 'nb_books' => 100),
        array('id' => 2, 'picture' => '/images/william-shakespeare.jpg','username' => ' William Shakespeare', 'email' =>  ' william.shakespeare@gmail.com', 'nb_books' => 200 ),
        array('id' => 3, 'picture' => '/images/Taha_Hussein.jpg','username' => 'Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300),
        );

    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }
    #[Route('/showauthor/{name}', name: 'app_showauthor')]
    public function show($name): Response
    {
        return $this->render('author/show.html.twig', [
            'name' => $name
        ]);
    }

    #[Route('/showtableauthor', name: 'app_showtableauthor')]
    public function showtableauthor(): Response
    {

        
        return $this->render('author/showtableauthor.html.twig', [
            'authors' => $this->authors
        ]);
    }

    #[Route('/showbyid/{id}', name: 'showbyid')]
    public function showbyid($id): Response
    {   
        #var_dump($id).die();
        
        $author=null;
        foreach($this->authors as $authorD)
        {
         if($authorD['id']==$id)
         $author=$authorD;
        }
        #var_dump($author).die();
        return $this->render('author/showbyid.html.twig', [
            'author' => $author
        ]);
    }

    #[Route('/showdbauthor', name: 'showdbauthor')]
    public function showdbauthor(AuthorRepository $AuthorRepository): Response
    {
        $author=$AuthorRepository->findAll();

        return $this->render('author/showdbauthor.html.twig', [
            'author' => $author
        ]);
    }

    #[Route('/addauthor', name: 'addauthor')]
    public function addauthor(ManagerRegistry $ManagerRegistry): Response
    { 
        $x  = $ManagerRegistry->getManager();
        $author=new Author ();//si instance existe -> ajout sinon update 
        $author->setUsername("3a54new");
        $author->setEmail("3a54new@gmail.tn");
        $x->persist($author);
        $x->flush();
        return new Response("great add");
    }

    #[Route('/addformauthor', name: 'addformauthor')]
    public function addformauthor(ManagerRegistry $ManagerRegistry , Request $req): Response//Request :recuperation kan ki bch nabaath haja 
    {
        $x  = $ManagerRegistry->getManager();//on l'utilise a chaque fois on a delete , ajout ou update 
        $author=new Author ();
        $form=$this->createForm(AuthorType::class,$author);// a chaque fois anna author type on ajoute ::class 
        $form->handleRequest($req);//on dirait methode "POST"(Recuperatiion de données )
        if($form->isSubmitted()and $form->isValid()){
        $x->persist($author);
        $x->flush();//execution 
        return $this->redirectToRoute('showdbauthor');//bch yhezni lel page show ( ka enha href )
        }
        return $this->renderForm('author/addformauthor.html.twig', [
            'f' => $form
        ]);
    }

    #[Route('/editauthor/{id}', name: 'editauthor')]
    public function editauthor($id,AuthorRepository $AuthorRepository,ManagerRegistry $ManagerRegistry,Request $req): Response
    {
        //var_dump($id).die(); verification 
        $x  = $ManagerRegistry->getManager();
        $dataid=$AuthorRepository->find($id);
        //var_dump($dataid).die();
        $form=$this->createForm(AuthorType::class,$dataid);
        $form->handleRequest($req);
        if($form->isSubmitted()and $form->isValid()){
            $x->persist($dataid);
            $x->flush();//execution 
            return $this->redirectToRoute('showdbauthor');//bch yhezni lel page show ( ka enha href )
            }

        return $this->renderForm('author/editauthor.html.twig', [
            'x' => $form
        ]);
    }

    #[Route('/deleteauthor/{id}', name: 'deleteauthor')]
    public function deleteauthor($id,ManagerRegistry $managerRegistry,AuthorRepository $authorRepository): Response
    {
        $x  = $managerRegistry->getManager();
        $dataid=$authorRepository->find($id);
        $x->remove($dataid);
        $x->flush();
       return $this->redirectToRoute('showdbauthor');
    }
    /*******************ATELIER5******************************/

    #[Route('/triauthor', name: 'triauthor')]
    public function triauthor(AuthorRepository $AuthorRepository): Response
    {
        $author=$AuthorRepository->showbyordrealph();
        return $this->render('author/triauthor.html.twig', [
            'author' => $author
        ]);
    }

    #[Route('/minmax', name: 'minmax')]
    public function minmax(Request $request,AuthorRepository $authorRepository): Response
    {
        $form = $this->createForm(MinmaxType::class);
        $form->handleRequest($request);
        $author=$authorRepository->findAll();
        
        
        if ($form->isSubmitted() && $form->isValid()) {
            $min = $form['min']->getData();
            $max = $form['max']->getData();
            $author = $authorRepository->minmax($min,$max);
            
            return $this->render('author/showdbauthor.html.twig', [
                'author' => $author,
                
            
            ]);
           // print("done");
        }
        
        return $this->renderForm('author/minmax.html.twig', [
            'f' => $form,
            'author' => $author,
            
        ]);
    }

    #[Route('/deletenb0', name: 'deletenb0')]
    public function deletenb0(AuthorRepository $AuthorRepository,ManagerRegistry $managerRegistry): Response
    {  
        $author=$AuthorRepository->findAll();
        return $this->render('author/triauthor.html.twig', [
            'author' => $author,
        ]);
    }
}
