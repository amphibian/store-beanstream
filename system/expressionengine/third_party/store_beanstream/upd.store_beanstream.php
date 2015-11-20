<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
    This file is part of Beanstream Payment Gateway for Store add-on for ExpressionEngine.

    Beanstream Payment Gateway for Store is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Beanstream Payment Gateway for Store is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    Read the terms of the GNU General Public License
    at <http://www.gnu.org/licenses/>.
    
    Copyright 2015 Derek Hogue
*/

require(PATH_THIRD.'store_beanstream/config.php');

class Store_beanstream_upd { 

	var $version = STORE_BEANSTREAM_VERSION;
	var $module_name = 'Store_beanstream';
	 
	function __construct(){}
	
	function install()
	{	
		$data = array(
			'module_name' => $this->module_name,
			'module_version' => $this->version,
			'has_cp_backend' => 'n',
			'has_publish_fields' => 'n'
		);
		ee()->db->insert('modules', $data);
		
		$data = array(
			'class' => $this->module_name,
			'csrf_exempt' => 1,
			'method' => 'process_interac_response'
		);
		ee()->db->insert('exp_actions', $data);
		
		ee()->load->dbforge();
		ee()->dbforge->add_field(
			array(
				'timestamp' => array('type' => 'int', 'constraint' => '10', 'null' => FALSE),
				'interac_merchant_data' => array('type' => 'varchar', 'constraint' => '50', 'null' => FALSE),
				'interac_institution_name' => array('type' => 'varchar', 'constraint' => '255'),
				'interac_institution_confirmation_code' => array('type' => 'varchar', 'constraint' => '255'),
				'interac_auth_code' => array('type' => 'varchar', 'constraint' => '255'),
				'store_hash' => array('type' => 'varchar', 'constraint' => '32', 'null' => FALSE),
				'store_notify_url' => array('type' => 'varchar', 'constraint' => '255', 'null' => FALSE),
			)
		);
		ee()->dbforge->add_key(array('interac_merchant_data','store_hash'));
		ee()->dbforge->create_table('store_beanstream_interac');
				
		return TRUE;
	}

	
	function update($current = '')
	{
		if($current == $this->version)
		{
			return FALSE;
		}
		return TRUE;
	}
	
	
	function uninstall()
	{
		ee()->load->dbforge();
		ee()->dbforge->drop_table('store_beanstream_interac');
		ee()->db->delete('modules', array('module_name' => $this->module_name));
		ee()->db->delete('actions', array('class' => $this->module_name));
		return TRUE;
	}
}