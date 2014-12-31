<?php

namespace Bdloc\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

use Bdloc\AppBundle\Entity\User;
use Bdloc\AppBundle\Form\RegisterType;
use Bdloc\AppBundle\Form\DropSpotType;
use Bdloc\AppBundle\Form\UserType;
use Bdloc\AppBundle\Util\StringHelper;

class UserController extends Controller {

    /**
    * @Route("/inscription")
    */
    public function registerAction()
    {
        $params = array();

        $user = new User();
        $registerForm = $this->createForm(new registerType(), $user, array('validation_groups' => array('registration', 'Default')));

        //gère la soumission du form
        $request = $this->getRequest();
        $registerForm->handleRequest($request);
        if ($registerForm->isValid()){

            //on termine l'hydratation de notre objet User
            //avant enregistrement
            $user->setAddress( explode(',', $user->getAddress())[0] );
            //salt, token, password hashé
            //dates directement dans l'entité avec les lifecyclecallbacks
            $user->setRoles( array("ROLE_USER") );
            $user->setIsEnabled( 0 );  // on le passe à 1 en fin d'enregistrement, après étape 3 abonnement
            $user->setSubscriptionType("0");
            $user->setSubscriptionRenewal(new \DateTime());

            //notre propre classe
            $stringHelper = new StringHelper();

            //génère un salt et un token avec notre propre classe
            $user->setSalt( $stringHelper->randomString() );
            $user->setToken( $stringHelper->randomString(30) );

            //hashe le mot de passe (tiré de la doc.)
            //toujours donner un salt à l'user d'abord !
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
            $user->setPassword($password);

            //sauvegarde le user en base
            $em = $this->getDoctrine()->getManager();
            $em->persist( $user );
            $em->flush();

             // Créer un message qui ne s'affichera qu'une fois
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Bienvenue !'
            );

            //CONNEXION AUTOMATIQUE
            //tiré de http://stackoverflow.com/questions/9550079/how-to-programmatically-login-authenticate-a-user
            
            // "secured_area" est le nom du firewall défini dans security.yml
            $token = new UsernamePasswordToken($user, $user->getPassword(), "secured_area", $user->getRoles());
            $this->get("security.context")->setToken($token);

            // déclenche l'événement de login
            $event = new InteractiveLoginEvent($request, $token);
            $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

            // Mettre en session l'id de l'utilisateur
            $this->get('session')->set('id', $user->getId());

            //redirige vers l'accueil
            return $this->redirect( $this->generateUrl( "bdloc_app_user_pointrelais" ) );
        }

        $params['registerForm'] = $registerForm->createView();

        return $this->render("user/register.html.twig", $params);
    }

    /**
    * @Route("/pointrelais")
    */
    public function pointrelaisAction()
    {
     
        $params=array();
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

        // récupère l'adresse de l'utilisateur
        $add_user = $user->getAddress();

        $dropspotForm = $this->createForm(new DropSpotType(), $user);

        // Demande à SF d'injecter les données du formulaire dans notre entité ($user)
        $request = $this->getRequest();
        $dropspotForm->handleRequest($request);

        // Déclenche la validation sur notre entité ET teste si le formulaire est soumis
        if ($dropspotForm->isValid()) {

            // update en bdd pour DropSpotType
            $em = $this->getDoctrine()->getManager(); 
            $em->flush();

            // Créer un message qui ne s'affichera qu'une fois
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Point relais ajouté !'
            );
            
            // Redirection vers étape 3, choix du paiement
            return $this->redirect( $this->generateUrl("bdloc_app_payment_takesubscriptionpayment") );
            //return $this->redirect( $this->generateUrl("bdloc_app_user_showsubscriptionpaymentform", array('id' => $id)) );

        }


        $params['dropspotForm'] = $dropspotForm->createView();

        // Récupération des coord gps des points relais
        $dropSpotRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:DropSpot");
        $dropSpots = $dropSpotRepo->findAll();
        foreach ($dropSpots as $dropSpot) {
            $dropTab["nom"] = $dropSpot->getName();
            $dropTab["lat"] = $dropSpot->getLatitude();
            $dropTab["lng"] = $dropSpot->getLongitude();
            $dropTab["add"] = $dropSpot->getAddress();
            $dropTab["zip"] = $dropSpot->getZip();
            $params['dropSpots'][] = $dropTab;
        }

        $params['add_user'] = $add_user;
        //print_r($params);
        
        return $this->render("prerelais.html.twig", $params);
    }

}


