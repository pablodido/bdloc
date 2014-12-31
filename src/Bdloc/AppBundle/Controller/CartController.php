<?php

namespace Bdloc\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use \Bdloc\AppBundle\Entity\Cart;
use \Bdloc\AppBundle\Entity\CartItem;
use Bdloc\AppBundle\Entity\User;
use Bdloc\AppBundle\Entity\Book;

class CartController extends Controller {


    /**
     * @Route("/basket/retirer/{id}{idbook}")
     *
     */

     public function deleteItemAction($id,$idbook){

        $BookRepo=$this->getDoctrine()
        ->getRepository("BdlocAppBundle:Book");
        $book=$BookRepo->find($idbook);
        
      
       $bdStockOld=$book->getStock();
       $bdStockNew=$bdStockOld+1;
       $book->setStock($bdStockNew);

        $cartItemrepo = $this->getDoctrine()->getRepository("BdlocAppBundle:CartItem");
        $cartItem = $cartItemrepo->find($id);
        $em = $this->getDoctrine()->getManager();
        $em -> remove( $cartItem );
        $em-> persist($book);
        $em->flush();


        return $this->redirect($this->generateUrl("bdloc_app_default_basket"));


     }

 }