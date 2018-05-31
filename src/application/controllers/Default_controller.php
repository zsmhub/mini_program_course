<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Default_controller extends MY_Controller {

	public function index(){
		header("location:index.php?d=admin&c=home&a=index");
	}
}
