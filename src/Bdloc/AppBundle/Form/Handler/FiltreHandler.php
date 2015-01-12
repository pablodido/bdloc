<?php

namespace Bdloc\AppBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class FiltreHandler
{

	protected $form;
	protected $request;

	public function __construct(Form $form,request $request){

		$this->form=$form;
		$this->request= $request;

	}

	public function process(){

	}
	protected function onSuccess(){

	}
}