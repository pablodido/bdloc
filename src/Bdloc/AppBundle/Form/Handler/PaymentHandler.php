<?php

namespace Bdloc\AppBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class PaymentHandler
{

	protected $form;
	protected $request;
	protected $em;
	/**
	* @param  Form $form
	* @param Request $request
	**/

	public function __construct(Form $form,request $request, EntityManager $em){

		$this->form=$form;
		$this->request= $request;
		$this->em = $em;

	}

	/**
	* @return bool
	**/

	public function process(){

		  $this->CreditcardsForm->handleRequest($this->request);


		  if ($this->CreditcardsForm->isValid()){
		  		$this-> onSuccess();
		  		return true;
		  }
		  return false;

	}

	protected function onSuccess(){

		//$em = $this->getDoctrine()->getManager();
        $this->em->persist($creditCard);
        $this->em->persist($user);
        $this->em->flush();

	}
}