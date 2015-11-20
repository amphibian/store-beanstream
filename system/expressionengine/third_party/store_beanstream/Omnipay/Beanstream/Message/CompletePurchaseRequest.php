<?php

namespace Omnipay\Beanstream\Message;

/*
	Note that this class is used to complete Interac Online purchases only.
*/

class CompletePurchaseRequest extends AbstractRequest
{    
	public function sendData($data)
	{  
		
		if($data['interac_response']['funded'] == 0)
		{
			$result = array('error' => 'The Interac Online transaction was not completed.');
		}
		else
		{
			/*
		    	Initialize the Beanstream library and authenticate	
		    */
		    $beanstream = new \Beanstream\Gateway(
		    	$this->getActiveMerchantId(), 
		    	$this->getActiveApiKey(), 'www', 'v1'
		    );
		    		    
			try
			{
				$result = $beanstream->payments()->continuePayment($data, $this->getInteracMerchantData());
				/*
					We need to save this data in the database as we are required 
					to display it on the "success" screen,
					and Store offers no way to do this
				*/
				$record = array(
					'interac_institution_name' => $data['interac_response']['idebit_issname'],
					'interac_institution_confirmation_code' => $data['interac_response']['idebit_issconf'],
					'interac_auth_code' => $result['auth_code']
				);
				ee()->db->query(ee()->db->update_string('store_beanstream_interac', $record, array('interac_merchant_data' => $this->getInteracMerchantData())));
			}
			catch(\Beanstream\Exception $e)
			{
			    // exit(var_dump($e));
			    $result = array('error' => $e->getMessage());
			}
		}
		
        return $this->response = new Response($this, $result);
    }

    public function getData()
    {
		return array(
			'payment_method' => 'interac',
			'interac_response' => array(
				'funded' => ee()->input->get('funded'),
				'idebit_track2' => ee()->input->get('IDEBIT_TRACK2'),
				'idebit_isslang' => ee()->input->get('IDEBIT_ISSLANG'),
				'idebit_version' => ee()->input->get('IDEBIT_VERSION'),
				'idebit_issconf' => ee()->input->get('IDEBIT_ISSCONF'),
				'idebit_issname' => ee()->input->get('IDEBIT_ISSNAME'),
				'idebit_amount' => ee()->input->get('IDEBIT_AMOUNT'),
				'idebit_invoice' => ee()->input->get('IDEBIT_INVOICE')	
			)
		);
    }
}
