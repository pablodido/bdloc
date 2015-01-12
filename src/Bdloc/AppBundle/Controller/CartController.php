<?php

namespace Bdloc\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use \Bdloc\AppBundle\Entity\Cart;
use \Bdloc\AppBundle\Entity\CartItem;

use Symfony\Component\HttpFoundation\Response;


class CartController extends Controller {


    
     /**
     * @Route("/supprimebd/{id}")
     */
    public function removeBookAction($id)
    {
        $params = array();
        // récupère l'utilisateur en session
        $user = $this->getUser();

        // enlève le cartItem 
        $cartItemRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:CartItem");
        $cartItem = $cartItemRepo->find( $id );

        $book = $cartItem->getBook();
        // remet le stock à jour
        $book->setStock( $book->getStock() + 1 );

        $em = $this->getDoctrine()->getManager(); 
        $em->remove($cartItem);  
        $em->persist($book);  
        $em->flush();

        $cartRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Cart");
        $cart = $cartRepo->findUserCurrentCart( $user );
        if (!empty($cart)) {
            $cart = $cartRepo->findBooksInCurrentCart( $cart->getId() );
        }
        $params['cart'] = $cart;
        
         return $this->redirect($this->generateUrl("bdloc_app_default_basket"), $params);
    }

     /**
     * @Route("/addbook/{book_id}")
     */
    public function addbookAction($book_id){

        $user = $this->getUser();
        //contient les paramètres pour Twig
        $params = array();

       //  check dans table cart s'il a un panier en cours (statut = en cours, validé, vidé)
        //      si oui, récupérer le panier, sinon créer un panier
        //      puis l'hydrater et l'enregistrer

        $cartRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Cart");
        $cart = $cartRepo->findOneBy(
             array('user'=>$user)
        );
        
        // récupère le book à ajouter

        $BookRepo=$this->getDoctrine()->getRepository("BdlocAppBundle:Book");
        $book=$BookRepo->find($book_id); 

        

        if (empty($cart)){ 
            // Création de panier
            $cart = new Cart();
            $cart->setUser( $user );
            $cartItem = new CartItem();
            $cartItem->setCart( $cart );
            $cartItem->setBook( $book );

             // enlève une quantité au stock 
            $book->setStock( $book->getStock() - 1 );
            $bookStock = $book->getStock();

              // sauvegarde en bdd
            $em = $this->getDoctrine()->getManager();
            $em->persist($cart);  
            $em->persist($cartItem);  
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'BD ajoutée à votre panier !'
            );

        }

        elseif ( count($cart->getCartItems()) < 10 ) {
         // Maj de panier en cours
            $cartItem = new CartItem();
            $cartItem->setCart( $cart );
            $cartItem->setBook( $book );

            // enlève une quantité au stock 
            $book->setStock( $book->getStock() - 1 );
            $bookStock = $book->getStock();

            // maj dateModified
            $cart->setDateModified( new \DateTime() );

            // update cart et sauvegarde cartItem
            $em = $this->getDoctrine()->getManager(); 
            $em->persist($cart);  
            $em->persist($cartItem);  
            $em->flush();
        
            $message = $this->get('session')->getFlashBag()->add(
                'notice',
                'BD ajoutée à votre panier !'
            );
        }
        else {
            $message = $this->get('session')->getFlashBag()->add(
                'error',
                'Vous avez atteint le maximum de BD dans votre panier !'
            );
            $bookStock = $book->getStock();
        }    
            //vide le formulaire et empêche la resoumission des données
            return $this->redirect( $this->generateUrl("bdloc_app_default_index", $params)); 
    }

    

 }