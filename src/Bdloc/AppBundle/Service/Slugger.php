<?php

	namespace BDloc\AppBundle\Service;

	class Sluggger{

		public function __construct($yo)
		{
			$this->yo=$yo;
		}
		public function setBook($book){
			$this->book=$book;
		}
		public function test(){
			die('test');
		}

		public function sluggify($string){
			$slug=strlower($string);
			return $slug;
		}
	}