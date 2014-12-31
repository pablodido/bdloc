<?php

namespace Bdloc\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Transaction;
use Bdloc\AppBundle\Entity\User;
use Bdloc\AppBundle\Entity\Creditcard;
use Bdloc\AppBundle\Form\RegisterType;
use Bdloc\AppBundle\Util\StringHelper;
use Bdloc\AppBundle\Form\CreditCardsType;
//use PayPal\Rest\ApiContext;
//use PayPal\Auth\OAuthTokenCredential;
//use PayPal\Api\Address;
//use PayPal\Api\Details;
//use PayPal\Api\Item;
//use PayPal\Api\ItemList;
//use PayPal\Api\RedirectUrls;

class PaymentController extends Controller
{
    /**
     * @Route("/paiement")
     */
    public function takeSubscriptionPaymentAction()
    {
        // --------------------- FORMULAIRE PAIEMENT ---------------------
        // Récupère l'id utilisateur
        $id = $this->get('session')->get('id');

        $userRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:User");
        $user = $userRepo->find( $id );

        //\Doctrine\Common\Util\Debug::dump($user);

        if (empty($user)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Utilisateur non trouvé !'
            );     
            return $this->redirect( $this->generateUrl("bdloc_app_user_register") );       
        }
        //debut ajout carte

        $creditCard = new Creditcard;
        $params = array();
        $CreditcardsForm = $this->createForm(new CreditCardsType(), $creditCard);

        //soumission du form
        $request = $this->getRequest();
        $CreditcardsForm->handleRequest($request);
           // Déclenche la validation sur notre entité ET teste si le formulaire est soumis
        if ($CreditcardsForm->isValid()){

        // On récupère le prix avec le bouton radio rajouté manuellement dans le form!
            $typeAbo = $CreditcardsForm["abonnement"]->getData();
            if ($typeAbo == "A") {
                $prixAbo = $this->container->getParameter('prixAboA');
            }
            else if ($typeAbo == "M") {
                $prixAbo = $this->container->getParameter('prixAboM');
            }
            
            // Utilisation du service PPUtility
            $ppu = $this->get('paypal_utility');
            $ppu->setCreditCard($creditCard);
            $ppu->setPrixAbo($prixAbo);
            $statut = $ppu->createPayment();
            $paypalCC_id = $ppu->registerCreditCard();

         //ajout de la carte  en base
        $em = $this->getDoctrine()->getManager();
        $em->persist($creditCard);
        $em->persist($user);
        $em->flush();

        //redirige vers la page choix du point relais
            return $this->redirect( $this->generateUrl( "bdloc_app_default_index" ) );
       
      }
          $params['CreditcardsForm'] = $CreditcardsForm->createView();

          return $this->render("security/paiement.html.twig", $params);
  
      
    }

        
}