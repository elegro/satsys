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
		if($_SERVER['REQUEST_METHOD'] != "POST" || IS_AJAX)
		{
			$erro = true;
			$msj = "El ingreso no es correcto...";
			do{
				echo $msj;
				echo "------ERROR ACCESO NO PERMITIDO-----";
				$msj = "";
			}while($erro == true);
		}else{
			try {
				$this->input->get_params();
				$usurio =  $this->input->param("usuario");
				$clave = $this->input->param("clave");
				$this->form_validation->set_rules("usuario","nombre de usuario","required");
				$this->form_validation->set_rules("clave","clave de acceso","required");
				
				if ($this->form_validation->run() == FALSE)
				{
					$msj = "Todos los campos son requeridos por el sistema para iniciar sesion con exito.\n".
					form_error('usuario', '<p class="text-danger">', '</p>')."\n".
					form_error('clave', '<p class="text-danger">', '</p>');
					throw new Exception($msj, 1);
				} else {
					$password = $this->auth_model->encrypt_password(
						trim($this->input->post("usuario")), 
						trim($this->input->post("clave")), 
						'PHP5.3MD5'
					);
					$rqs = $this->auth_model->validar_usuario($password, $this->input->post("usuario"));
					if(!$rqs){
						throw new Exception("Las credenciales de acceso no son correctas para el ingreso.", 1);
					}
					
					$token = $this->jwt->create(array(
						"usuario"=> $this->input->param("usuario"),
						"moment"=> date('Y-m-d H:i:s')
					));
					$this->toJson(array("token"=> $token));
				}
			} catch (\Exception $err) {
				$this->session->flashdata('auth', array(
					"success"=> false, 
					"msj"=> $err->getMessage()
				));
				redirect("autenticar_error", "location");
			}
		}
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
	}


}