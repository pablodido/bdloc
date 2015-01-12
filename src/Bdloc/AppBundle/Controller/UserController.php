<?php

namespace Bdloc\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

use Bdloc\AppBundle\Form\RegisterType;
use Bdloc\AppBundle\Form\DropSpotType;
use Bdloc\AppBundle\Form\CreditCardType;
use Bdloc\AppBundle\Entity\User;
use Bdloc\AppBundle\Entity\CreditCard;
use Bdloc\AppBundle\Form\UserType;
use Bdloc\AppBundle\Util\StringHelper;
use Bdloc\AppBundle\Form\ForgotPasswordStepOneType;
use Bdloc\AppBundle\Form\ForgotPasswordStepTwoType;

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

    /**
     * @Route("/mot-de-pass-oublie/etape1")
     */
    public function forgotPasswordStepOneAction() {

        $params = array();

        // --------------------- FORMULAIRE FORGOT PASSWORD 1 ---------------------
        $user = new User();
        $forgotPasswordStepOneForm = $this->createForm(new ForgotPasswordStepOneType(), $user);

        // Demande à SF d'injecter les données du formulaire dans notre entité ($user)
        $request = $this->getRequest();
        $forgotPasswordStepOneForm->handleRequest($request);

        // Déclenche la validation sur notre entité ET teste si le formulaire est soumis
        if ($forgotPasswordStepOneForm->isValid()) {

            // on vérifie que l'email existe
            $userRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:User");
            $userFound = $userRepo->findOneByEmail( $user->getEmail() );
            
            // si userFound on récupère la token correspondante et on envoie un email
            if ($userFound) {

                $links = $this->generateUrl("bdloc_app_user_forgotpasswordsteptwo", array("email" => $userFound->getEmail(), "token" => $userFound->getToken()), true);
                $params_message['links'] = $links;

                $message = \Swift_Message::newInstance()
                    ->setSubject('Nouveau mot de passe sur BDLOC')
                    ->setFrom('admin@bdloc.com')
                    ->setTo( $userFound->getEmail() )
                    ->setContentType('text/html')
                    ->setBody($this->renderView('emails/forgot_password_email.html.twig', $params_message));
                $this->get('mailer')->send($message);

                // Créer un message qui ne s'affichera qu'une fois
                $this->get('session')->getFlashBag()->add(
                    'notice',
                    'Un email de modification de mot de passe vous a été envoyé !'
                );
              
                // Redirection vers l'accueil
                return $this->redirect( $this->generateUrl("bdloc_app_default_home") );
            }
            else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Email inconnu !'
                );
            }

        }

        $params['forgotPasswordStepOneForm'] = $forgotPasswordStepOneForm->createView();

        return $this->render("user/forgot_password_step_one.html.twig", $params);
    }

    /**
     * @Route("/mot-de-pass-oublie/etape2/{email}/{token}")
     */
    public function forgotPasswordStepTwoAction($email, $token) {

        $params = array();

        // on récupère l'utilisateur pour vérifier que l'email et la token correspondent
        $userRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:User");
        $userFound = $userRepo->findOneByEmail( $email );

        if ( $token === $userFound->getToken() ) {

            // --------------------- FORMULAIRE FORGOT PASSWORD 2 ---------------------
            //$user = new User();
            $forgotPasswordStepTwoForm = $this->createForm(new ForgotPasswordStepTwoType(), $userFound);

            // Demande à SF d'injecter les données du formulaire dans notre entité ($user)
            $request = $this->getRequest();
            $forgotPasswordStepTwoForm->handleRequest($request);

            // Déclenche la validation sur notre entité ET teste si le formulaire est soumis
            if ($forgotPasswordStepTwoForm->isValid()) {

                // on régénère token et mot de passe hashé
                $stringHelper = new StringHelper(); 
                $userFound->setToken( $stringHelper->randomString(30) ); 

                // Hasher mot de passe
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($userFound);
                $password = $encoder->encodePassword($userFound->getPassword(), $userFound->getSalt());
                $userFound->setPassword($password);

                // on update en BDD
                $em = $this->getDoctrine()->getManager(); 
                $em->flush();
                
                // Créer un message qui ne s'affichera qu'une fois
                $this->get('session')->getFlashBag()->add(
                    'notice',
                    'Votre mot de passe a été changé !'
                );
              
                // Redirection vers le login
                return $this->redirect( $this->generateUrl("bdloc_app_user_login") );


            }

            $params['forgotPasswordStepTwoForm'] = $forgotPasswordStepTwoForm->createView();

            return $this->render("user/forgot_password_step_two.html.twig", $params);
        }
        else {
            // Redirection vers l'accueil
            return $this->redirect( $this->generateUrl("bdloc_app_default_home") );
        }
    }

    /**
     * @Route("/abonnement/fin-de-validite")
     */
    public function checkSubscriptionAction() {

        $params = array();
        // récupère l'utilisateur en session
        $user = $this->getUser();

        // Si user a une amende, appelle directement le paiement (il faudrait que la méthode redirige vers ici...pb avec le foreach...)
        $fineRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Fine");
        $fines = $fineRepo->findUserFines( $user );

        if (!empty($fines)) {
            foreach ($fines as $fine) {
                $params = array(
                    "fineId" => $fine->getId(),
                    "cout" => $fine->getAmount(),
                    );
                $url = $this->generateUrl("bdloc_app_payment_takeautomaticalfinepayment", $params);
                return $this->redirect($url);
            }
        }

        // on récupère l'utilisateur pour vérifier que l'email et la token correspondent
        $subscriptionRenewal = $user->getSubscriptionRenewal();
        $today = new \DateTime("-1 day");
        if ( $subscriptionRenewal < $today){
            // mettre en ROLE_USER_EXPIRED
            $user->setRoles( array("ROLE_USER_EXPIRED") );
            $em = $this->getDoctrine()->getManager(); 
            $em->persist($user); 
            $em->flush();
            $em->refresh( $user );

            return $this->render("user/check_subscription.html.twig", $params);
        }
        else {
            // Redirection vers le catalogue
            return $this->redirect( $this->generateUrl("bdloc_app_book_catalogredirect") );
        }
    }
    /**
     * @Route("/abonnement/renouvellement")
     */
    public function updateSubscriptionAction() {

        $params = array();
        $user = $this->getUser();

        // --------------------- FORMULAIRE RENOUVELLEMENT ---------------------
        /*$creditCards = $user->getCreditCards();
        //dump($creditCards);
        //die();
        foreach ($creditCards as $creditCard) {
            //@todo attention, quelle carte de crédit ??
            //
            $userCCDate = $creditCard->getValidUntil();
        }
        //dump( $userCCDate );
        //die();*/
        
        $creditCardRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:CreditCard");
        $creditCard = $creditCardRepo->findLastCreditCardWithUserId( $user->getId() );
        $userCCDate = $creditCard->getValidUntil();

        $today = new \DateTime("-1 day");
        if ( $userCCDate < $today ){
            // Reprendre une nouvelle carte
            // Mettre en session l'id de l'utilisateur pour la redirection vers showsubscriptionpaymentform
            $this->get('session')->set('id', $user->getId());
            return $this->redirect( $this->generateUrl("bdloc_app_user_showsubscriptionpaymentform") );

        } else {
            // new paiement
            $typeAbo = $user->getSubscriptionType();
            return $this->redirect( $this->generateUrl("bdloc_app_payment_takepayment", array("type" => $typeAbo)) );
        }
    }

    /**
     * @Route("abonnement/a-bientot")
     */
    public function unsubscribeAction()
    {
        // récupère l'utilisateur en session
        $user = $this->getUser();

        $params_message = array(
            "username" => $user->getUsername(),
            "email" => $user->getEmail(),
            "firstName" => $user->getFirstName(),
            "lastName" => $user->getLastName(),
        );

        // Envoyer un mail à l'admin
        $messageMail = \Swift_Message::newInstance()
            ->setSubject('Désabonnement sur BDloc')
            ->setFrom('admin@bdloc.com')
            ->setTo( 'sweetformation@yahoo.fr' )
            ->setContentType('text/html')
            ->setBody($this->renderView('emails/unsubscribe_email.html.twig', $params_message));
        $this->get('mailer')->send($messageMail);
        
        // User->setIsEnabled à 0 !
        $user->setIsEnabled(0);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        $em->refresh( $user );

        // rediriger vers default home
        //return $this->redirect($this->generateUrl("bdloc_app_default_home"));
        return $this->redirect($this->generateUrl("logout"));
    }


}


