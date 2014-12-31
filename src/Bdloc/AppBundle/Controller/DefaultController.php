<?php

namespace Bdloc\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Bdloc\AppBundle\Form\RegisterType;
use Bdloc\AppBundle\Form\UserType;
use Bdloc\AppBundle\Entity\CartItem;
use Bdloc\AppBundle\Entity\Cart;
use Bdloc\AppBundle\Entity\User;
use Bdloc\AppBundle\Entity\Book;
use \DateTime;
use \DateInterval;


class DefaultController extends Controller
{
    
    /**
     * @Route("/")
     */
    public function homeAction()
    {
        /*$slugger = $this->get('bd.slugger');
        $slug = $slugger->sluggify("ASKDH354DQF324fdqfqsf");
        echo $slug;

        echo $slugger->yo;

        print_r($slugger);
        $slugger->test();*/

        if (!empty($this->getUser())) {
            return $this->redirect($this->generateUrl("bdloc_app_default_index"));
        }
        return $this->render("default/home.html.twig");
    }


    /**
     * @Route("/bd/{page}",defaults={"page":1})
     * 
     */
    //@Route("/{page}",requirements={"page":"\d+"}
    public function indexAction($page)
    {
       //contient les paramètres 
        $params=array();

            $em = $this->getDoctrine()->getManager();
            $dql   = "SELECT b FROM BdlocAppBundle:Book b";
            $query = $em->createQuery($dql);

            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $query,
                $this->get('request')->query->get('page', $page)/*page number*/,
                20/*limit per page*/
            );
        $params['pagination']=$pagination;   
        return $this->render("book/catalog.html.twig",$params); 
    }

    /**
     * @Route("/detail/{id}")
     * 
     */
    public function detailAction($id)
    {
       //contient les paramètres 
        $params=array();

        $BookRepo=$this->getDoctrine()
        ->getRepository("BdlocAppBundle:Book");
        $book=$BookRepo->find($id);
        $params['book']=$book;

        return $this->render("book/detail.html.twig",$params); 
    }
    /**
     * @Route("/addbook/{id}")
     */
    public function addbookAction($id)
    {

        $user = $this->getUser();
        //contient les paramètres pour Twig
        $params = array();

        $BookRepo=$this->getDoctrine()
        ->getRepository("BdlocAppBundle:Book");
        $book=$BookRepo->find($id); 

        $bdStockOld=$book->getStock();
        $bdStockNew=$bdStockOld-1;
        $book->setStock($bdStockNew);

        $cartRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Cart");
        $newCart = $cartRepo->findOneBy(
             array('user'=>$user,'status'=>"en cours")
        );

        if (! $newCart){ 
            $newCart = new Cart();
        }
        $newItem = new CartItem();
        
        $newItem->setCart($newCart);
        $newItem->setBook($book);// relation  many to one 
        $newCart->addCartitem($newItem); // relation one to many
        $newItem->setDateModified(new \DateTime);
        $newItem->setDateCreated(new \DateTime);
        $newCart->setDateModified(new \DateTime);
        $newCart->setDateCreated(new \DateTime);
        $newCart->setStatus("en cours");
        $newCart->setUser($user);
    
              //sauvegarder en bdd
            $em = $this->getDoctrine()->getManager();
            $em->persist( $newItem);
            $em->persist( $newCart);
            $em-> persist($book);
            $em->flush();
          

            //crée un message qui ne s'affichera qu'une fois
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Votre BD ajouté!'
            );

            //vide le formulaire et empêche la resoumission des données
            return $this->redirect( $this->generateUrl("bdloc_app_default_index", $params)); 
    }

    /**
     * @Route("/basket/")
     */
    public function basketAction()
    {
         $user = $this->getUser(); 
         //contient les paramètres pour Twig
        $params = array();

        $cartRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Cart");
        $cart = $cartRepo->findOneBy(
             array('users'=>$user,'status'=>"en cours")
        );

        if (! $cart){
            $dateDelivery = new DateTime();
            $dateDelivery=$dateDelivery->add(new DateInterval('P3D'));
            $dateReturn = new DateTime();
            $dateReturn=$dateReturn->add(new DateInterval('P14D'));

             $cart = new Cart();
             $cart -> setUser($user);
             $cart -> setStatus('en cours');
             $cart ->setDateModified(new \DateTime());
             $cart ->setDateCreated(new \DateTime());
             $cart ->setDateDelivery($dateDelivery);
             // $cart ->setDateReturn($dateReturn);
             $em = $this->getDoctrine()->getManager();
             $em->persist($cart);
             $em->flush();
        };

        $nb=$cart->getCartItems();

        $nb=count($nb);

        $params = array (
            "cart" => $cart,
            "nb" =>$nb,
        );
        return $this->render("basket.html.twig", $params);
    }
    
    
    /**
     * @Route("/sedesabonner/")
     */
    public function SedesabonnerAction()
    {
         //contient les paramètres pour Twig
        $params = array();
        $bdrepo = $this->getDoctrine()
        ->getRepository('BdlocAppBundle:Book');

        return $this->render("account/sedesabonner.html.twig", $params);
    }

    /**
     * @Route("/historique/" )
     */
    public function historicAction()
    {
         //contient les paramètres pour Twig
        
        $params = array();
        $user = $this->getUser();

        $cartRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Cart");
        $cart = $cartRepo->findBy(array('user'=>$user, 'status'=>'en cours'),array('dateModified'=>'DESC'),5);

        $params = array (
            "carts" => $cart,
            "user" => $user);


        return $this->render("account/historic.html.twig", $params);
    }


}