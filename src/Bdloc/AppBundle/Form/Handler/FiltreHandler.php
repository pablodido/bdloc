<?php

namespace Bdloc\AppBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class FiltreHandler
{

	protected $form;
	protected $request;
	
	/**
	* @param  Form $form
	* @param Request $request
	**/

	public function __construct(Form $form,request $request){

		$this->form=$form;
		$this->request= $request;
		

	}


	public function process(){
		$this->filterForm->handleRequest($this->request);
		$this->$filterForm;

	}
	protected function onSuccess(){

	}
}