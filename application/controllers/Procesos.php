<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Procesos extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->auth();
		$this->set_template("usuario");
	}

	public function index()
	{
		//Procesos
		$this->input->param("nombre");
		//$this->set_title("Procesos Usuarios");
		//$this->render("procesos/index");
		$data = $this->input->get_params();
		$this->toJson($data);
	}

	public function crear()
	{
		$data = $this->input->get_params();
		$this->toJson($data);
	}

	public function mostrar($offset=0)
	{
		$this->toJson(array(
			"salida"=> "ok",
			"status"=> 200,
			"mostrar"=> 1
		));
	}

	public function actualizar($offset=0)
	{
		$this->input->param("nombre");
		$data = $this->input->get_params();
		$this->toJson($data);
	}

	public function remover($offset=0)
	{
		$this->toJson(array(
			"salida"=> "ok",
			"status"=> 200,
			"remover"=> 1
		));
	}

}