<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Procesos extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

    public function buscar_procesos()
    {
        $data = $this->db->query("SELECT * FROM user__usuarios");
        $data = $data->get();
    }
    
    public function load()
    {
        echo "OK model";
    }
    
}