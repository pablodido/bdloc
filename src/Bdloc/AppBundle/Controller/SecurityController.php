<?php

namespace Bdloc\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

use PayPal\Api\Amount;
use PayPal\Api\CreditCard;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Transaction;
//use PayPal\Rest\ApiContext;
//use PayPal\Auth\OAuthTokenCredential;
//use PayPal\Api\Address;
//use PayPal\Api\Details;
//use PayPal\Api\Item;
//use PayPal\Api\ItemList;
//use PayPal\Api\RedirectUrls;

class SecurityController extends Controller {

    /**
    * @Route("/login")
    */
    public function loginAction(Request $request)
        {
            $session = $request->getSession();

            // get the login error if there is one
            if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
                $error = $request->attributes->get(
                    SecurityContextInterface::AUTHENTICATION_ERROR
                );
            } elseif (null !== $session && $session->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
                $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
                $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
            } else {
                $error = '';
            }

            // last username entered by the user
            $lastUsername = (null === $session) ? '' : $session->get(SecurityContextInterface::LAST_USERNAME);

           
               $params= array(
                    // last username entered by the user
                    'last_username' => $lastUsername,
                    'error'         => $error,
                );
            
             return $this->render("user/login.html.twig", $params);
        }
    /**
     * Route("/paiement/")
     */
    public function takeSubscriptionPaymentAction()
    {
        //see kmj/paypalbridgebundle
        $apiContext = $this->get('paypal')->getApiContext();

       // ### CreditCard
       // A resource representing a credit card that can be
       // used to fund a payment.
       $card = new CreditCard();
       $card->setType("visa");
       $card->setNumber("4417119669820331");
       $card->setExpire_month("11");
       $card->setExpire_year("2018");
       $card->setCvv2("987");
       $card->setFirst_name("Joe");
       $card->setLast_name("Shopper");

       // ### FundingInstrument
       // A resource representing a Payer's funding instrument.
       // Use a Payer ID (A unique identifier of the payer generated
       // and provided by the facilitator. This is required when
       // creating or using a tokenized funding instrument)
       // and the `CreditCardDetails`
       $fi = new FundingInstrument();
       $fi->setCredit_card($card);

       // ### Payer
       // A resource representing a Payer that funds a payment
       // Use the List of `FundingInstrument` and the Payment Method
       // as 'credit_card'
       $payer = new Payer();
       $payer->setPayment_method("credit_card");
       $payer->setFunding_instruments(array($fi));

       // ### Amount
       // Let's you specify a payment amount.
       $amount = new Amount();
       $amount->setCurrency("EUR");
       $amount->setTotal("12.00");

       // ### Transaction
       // A transaction defines the contract of a
       // payment - what is the payment for and who
       // is fulfilling it. Transaction is created with
       // a `Payee` and `Amount` types
       $transaction = new Transaction();
       $transaction->setAmount($amount);
       $transaction->setDescription("This is the payment description.");

       // ### Payment
       // A Payment Resource; create one using
       // the above types and intent as 'sale'
       $payment = new Payment();
       $payment->setIntent("sale");
       $payment->setPayer($payer);
       $payment->setTransactions(array($transaction));

       // ### Create Payment
       // Create a payment by posting to the APIService
       // using a valid ApiContext
       // The return object contains the status;
        try {
            $result = $payment->create($apiContext);
            print_r($result);

        } catch (\Paypal\Exception\PPConnectionException $pce) {
            print_r( json_decode($pce->getData()) );
        }

        die();
    }


}