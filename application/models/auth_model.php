<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model
{

	public function encrypt_password($usuario='', $clave, $tipo_encriptado='')
	{
		$salt = substr($usuario, 0, 2);
		if($tipo_encriptado == ''){
			$tipo_encriptado = "PHP5.3MD5";
		}
		if($tipo_encriptado == 'MD5'){
			$salt = '$1$' . $salt . '$';
		}elseif($tipo_encriptado == 'BLOWFISH'){
			$salt = '$2$' . $salt . '$';
		}elseif($tipo_encriptado == 'PHP5.3MD5'){
			$salt = '$1$' . str_pad($salt, 10, '0');
		}
		$encrypted_password = crypt($clave, $salt);
		return $encrypted_password;
	}

	public function get_collection_usuarios()
	{
		$query = $this->db->get("gener02");
		return (is_bool($query))? $query : $query->result();
	}

	public function validar_usuario($usuario, $clave)
	{
		$this->db->select("*");
		$this->db->from("gener02");
		$this->db->where("usuario", $usuario);
		$this->db->where("clave", $clave);
		$query = $this->db->get();
		return (is_bool($query))? $query : $query->result();
	}
}


