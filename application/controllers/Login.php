<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function index()
	{
		$this->auth();
		$nombre = $this->input->param("nombre");
		$data = $this->input->get_params();
		$this->toJson($data);
	}

	public function autenticacion()
	{
		$data = $this->input->get_params();
		$token = $this->jwt->create(array(
			"usuario"=> $this->input->param("usuario"),
			"moment"=> date('Y-m-d H:i:s')
		));
		$data['token']= $token;
		$this->toJson($data);
	}

	public function validate_token()
	{
		$token = $this->input->param("token");
		$data =  $this->jwt->show($token);
		$this->toJson($data);
	}

	public function logout()
	{
	}

	public function autenticar_error()
	{
		$data =  $this->session->flashdata('auth');
		$this->toJson($data);
		// echo "{\"status\":\"301\", \"responseText\":\"Error Validaciòn Token\"}";
	}


}