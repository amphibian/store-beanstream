<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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

class Store_beanstream
{
	
	/*
		Display the required data from a successful Interac payment 
		on the order confirmation screen.
		
		Relevant variables:
		{interac_institution_name}
		{interac_institution_confirmation_code}
		{interac_auth_code}
	*/
	public function interac_response()
	{
		if($hash = ee()->TMPL->fetch_param('order_hash'))
		{
			ee()->db->where(array(
				'store_hash' => $hash,
				'interac_auth_code !=' => ''
			));
			ee()->db->order_by('timestamp', 'desc');
			$transaction = ee()->db->get('store_beanstream_interac');
			if($transaction->num_rows() == 1)
			{
				return ee()->TMPL->parse_variables(ee()->TMPL->tagdata, $transaction->result_array());
			}
		}
	}
	
	/*
		Read, process, and redirect the response from an Interac Online payment	.
	*/
	public function process_interac_response()
	{
		$idebit_merchantdata = strtolower(ee()->input->get_post('IDEBIT_MERCHDATA'));
		if($idebit_merchantdata)
		{
			$transaction = ee()->db->select('store_notify_url')->from('store_beanstream_interac')->where('interac_merchant_data', $idebit_merchantdata)->get();
			if($transaction->num_rows() == 1)
			{
				$_POST['funded'] = $_GET['funded'];
				$url = $transaction->row('store_notify_url').'&'.http_build_query($_POST);
				ee()->functions->redirect($url);
			}
		}
	}
	
	/*
		Beanstream requires that expiration years for credit cards be in 2-digit format.
		Store uses 4-digit format, so we need to replace the default options with this function.	
	*/
	public function year_options()
	{
		$out = '';
        for($i = gmdate('y'); $i <= (gmdate('y') + 9); $i++)
        {
            $out .= '<option value="'.$i.'">20'.$i.'</option>';
        }
        return $out;	
	}
   
}