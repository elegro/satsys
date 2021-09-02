<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once 'Firabase/JWT.php';
use \Jwt\Firabase\JWT;

class CI_Jwt
{
	private $http_cliente;
	private $http_cliente_origen;
	private $tipo_autenticacion;
	private $tipo;
	private $server;
	private $token_key  = null;
	private $token_encrypt  = null;
	private $token_auth     = null;
	private $token_expire   = 3600;
	private $db;

	public function __construct()
	{
		$cf =& load_class('Config', 'core');
		$this->uri =& load_class('URI', 'core');
		$this->CI =& get_instance();
		$this->db = $this->CI->load->database('default', true);
		$this->Initialize($cf->config);
		$this->db->close();
	}

	private function _db_buscar_token($token)
	{
		$db = $this->db->query("SELECT * FROM jwt__ingreso where token = '{$token}'");
		return ($db)? $db->feth_row() : false;
	}

	private function _db_guardar_token($token)
	{
		$dia = date('Y-m-d');
		$hora = date('H:i:s');
		$this->db->query("INSERT INTO jwt__ingreso (
			token, 
			dia, 
			hora, 
			http_cliente, 
			http_cliente_origen,
			consumo,
			estado
		)VALUES(
			'{$token}', 
			'{$dia}', 
			'{$hora}', 
			'{$this->http_cliente}', 
			'{$this->http_cliente_origen}',
			'1',
			'A'
		)");
	}

	//una vez el token es valido este debe aumentar el consumo
	private function _db_consumo_token($token, $aumento)
	{
		$this->db->query("UPDATE jwt__ingreso SET consumo='{$aumento}' WHERE token='{$token}' AND estado='A'");
	}

	//actulizar token caducado
	private function _db_caduca_token($token)
	{
		$this->db->query("UPDATE jwt__ingreso SET estado='I' WHERE token='{$token}' AND estado='A'");
	}


	public function Initialize($config)
	{
		$this->tipo          = ENVIRONMENT;
		$this->server        = $config['server'];
		$this->token_key     = $config['jwt']['key'];
		$this->token_encrypt = $config['jwt']['encrypt'];
		$this->token_expire  = (int) $config['jwt']['expire'];
		if(!$this->token_auth) $this->cliente();
		log_message('info', 'JWT Initialized');
	}

	public function create($data)
	{
		$data["tipo"] = $this->tipo;
		$data["server"] = $this->server;
		$time = time();
		$token = array(
			'exp'   => $time + $this->token_expire,
			'aud'   => $this->token_auth,
			'data'  => $data
		);
		$token = JWT::encode($token, $this->token_key);
		$this->_db_guardar_token($token);
		return $token;
	}

	public function check($token)
	{
		$response = "";
		try {
			if(empty($token)){
				throw new Exception("Token proporcionado no válido.", 1);
			}else{
				$decode = JWT::decode($token, $this->token_key, $this->token_encrypt);
				if($decode->aud !== $this->token_auth)
				{
					//Esta ingresando desde otra instancia. //el token ya no es valido
					$this->_db_caduca_token($token);
					throw new Exception("Invalido el ingreso de usuario.", 2);
				}else{
					$db = $this->_db_buscar_token($token);
					$aumento = 1 + ($db)? $db->consumo : 0;
					$this->_db_consumo_token($token, $aumento);
					$response = array("status"=> 200, "msj"=> "OK es valido");
				}
			}
		}catch(Exception $e){
			$response = array("status"=> 300, "msj"=> $e->getMessage());
		}catch(UnexpectedValueException $e){
			$response = array("status"=> 301, "msj"=> $e->getMessage(), "linea"=> $e->getLine(), "file"=> $e->getFile(), "code"=> $e->getCode());
		}
		return $response;
	}

	public function show($token)
	{
		$datos = JWT::decode($token,
			$this->token_key,
			$this->token_encrypt)->data;
		return $datos;
	}

	private function cliente()
	{
		$token_auth = '';
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$token_auth = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$token_auth = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$token_auth = $_SERVER['REMOTE_ADDR'];
		}
		$this->http_cliente = $token_auth;
		$this->http_cliente_origen = (isset($_SERVER['HTTP_USER_AGENT']))? $_SERVER['HTTP_USER_AGENT']: "Desconocido"; 
		$this->tipo_autenticacion = (isset($_SERVER['AUTH_TYPE'])?  $_SERVER['AUTH_TYPE']: "Desconocido");

		$token_auth .= "|". $this->http_cliente_origen; 
		$token_auth .= "|". gethostname(); 
		$this->token_auth = sha1($token_auth);
	}

	private function new_key($size=10)
	{
		$cadena = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890*#%&@|_.';
		$size_string = strlen($cadena);
		$pass="";
		for($i=1; $i <= $size; $i++){
			$pos = rand(0, $size_string - 1);
			$pass.= substr($cadena, $pos, 1);
		}
		return $pass;
	}

	public function get_http_cliente()
	{
		return $this->http_cliente;
	}

	public function get_tipo_autenticacion()
	{
		return $this->tipo_autenticacion;
	}

	public function get_http_cliente_origen()
	{
		return $this->http_cliente_origen;
	}

}