<?php

namespace Bdloc\AppBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class FiltreHandler
{

	protected $form;
	protected $request;
	$this->em = $em;
	
	/**
	* @param  Form $form
	* @param Request $request
	* @param Request $request
	**/

	public function __construct(Form $form,request $request, EntityManager $em){

		$this->form=$form;
		$this->request= $request;
		$this->em = $em;
		

	}


	public function process(){
		$this->$editPasswordForm->handleRequest($request);
		if ($this->$editPasswordForm->isValid()){
		  		
		  		return true;
		  }
		  return false;


	}
	protected function onSuccess(){

	}
}