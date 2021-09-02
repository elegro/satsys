<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Reporte_errors extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->set_template("usuario");
	}

	public function index()
	{ 
        echo "OK";
	}

}