<?php

namespace Omnipay\Beanstream\Message;

class PurchaseRequest extends AbstractRequest
{    
	public function sendData($data)
	{  
		// exit(var_dump($data));

	    /*
	    	Initialize the Beanstream library and authenticate	
	    */
	    $beanstream = new \Beanstream\Gateway(
	    	$this->getActiveMerchantId(), 
	    	$this->getActiveApiKey(), 'www', 'v1'
	    );
	    	    
		/*
			Make the appropriate payments request	
		*/
		try
		{
			if($this->getLegatoToken() && $data['payment_method'] == 'token')
			{
			   /*
			   		Make a token payment	
			   */
			   $result = $beanstream->payments()->makeLegatoTokenPayment($this->getLegatoToken(), $data, ($this->getPreAuth() == 'y') ? false : true);		    
			}
			elseif($data['payment_method'] == 'card')
			{
				/*
					Make a card payment	
				*/
				$result = $beanstream->payments()->makeCardPayment($data, ($this->getPreAuth() == 'y') ? false : true);
		   	}
		    else
		    {
			    /*
					Make an Interac payment	
			   */
			    $result = $beanstream->payments()->makePayment($data);	
			    /*
					We need to save some variables to the database here,
					otherwise we'll never know what order this was for when it comes back	
				*/
			    $order = $this->getOrder();
				$data = array(
					'timestamp' => ee()->localize->now,
					'interac_merchant_data' => $result['merchant_data'],
					'store_hash' => $order->order_hash,
					'store_notify_url' => $this->getNotifyUrl()
				);
				ee()->db->query(ee()->db->insert_string('store_beanstream_interac', $data));		    
		    }
		}
		catch(\Beanstream\Exception $e)
		{
		    // exit(var_dump($e));
		    $result = array('error' => $e->getMessage());
		}

        return $this->response = new Response($this, $result);
    }

    public function getData()
    {
        
        /*
        	Build a couple of clean address arrays	
        */
		$billingAddress = array(
			'name' => $this->getCard()->getBillingName(),
			'email_address' => $this->getCard()->getEmail(),
			'phone_number' => $this->getCard()->getBillingPhone(),
	        'address_line1' => $this->getCard()->getBillingAddress1(),
	        'address_line2' => $this->getCard()->getBillingAddress2(),
	        'city' => $this->getCard()->getBillingCity(),
	        'province' => $this->getCard()->getBillingState(),
	        'postal_code' => $this->getCard()->getBillingPostcode(),
	        'country' => $this->getCard()->getBillingCountry()
		);
		if(empty($billingAddress['province']))
		{
			$billingAddress['province'] = '--';
		}
		foreach($billingAddress as $k => $v)
		{
			if(empty($v))
			{
				unset($billingAddress[$k]);
			}
		}
		
		$shippingAddress = array(
			'name' => $this->getCard()->getShippingName(),
			'email_address' => $this->getCard()->getEmail(),
			'phone_number' => $this->getCard()->getShippingPhone(),
	        'address_line1' => $this->getCard()->getShippingAddress1(),
	        'address_line2' => $this->getCard()->getShippingAddress2(),
	        'city' => $this->getCard()->getShippingCity(),
	        'province' => $this->getCard()->getShippingState(),
	        'postal_code' => $this->getCard()->getShippingPostcode(),
	        'country' => $this->getCard()->getShippingCountry()
		);
		if(empty($shippingAddress['province']))
		{
			$shippingAddress['province'] = '--';
		}
		foreach($shippingAddress as $k => $v)
		{
			if(empty($v))
			{
				unset($shippingAddress[$k]);
			}
		}
		
		/*
			Common data for any purchase request	
		*/
		$data = array(
		    'order_number' => $this->getOrderId(),
		    'amount' => $this->getAmount(),
		    'ip_address' => $this->getClientIp(),
		    'billing' => $billingAddress,
		    'shipping' => $shippingAddress
		);
        
        if($this->getPaymentType() == 'interac')
        {
	        /*
	        	Add parameters we need for an Interac payment	
	        */
		    $data['language'] = $this->getLanguage();
		    $data['merchant_id'] = $this->getActiveMerchantId();
	        $data['payment_method'] = 'interac';
        }
        else
        {
	    	if( ! $this->getLegatoToken())
	    	{
		    	/*
		    		Add parameters we need for a standard credit card payment
		    	*/
		    	$this->getCard()->validate();
		    	
		    	$expiryMonth = str_pad($this->getCard()->getExpiryMonth(), 2, '0', STR_PAD_LEFT);
		    	$expiryYear = $this->getCard()->getExpiryYear();
		    	$expiryYear = (strlen($expiryYear > 2)) ? substr($expiryYear, strlen($expiryYear) - 2, 2) : $expiryYear;
		    	
				$card = array(
			        'name' => $this->getCard()->getName(),
			        'number' => $this->getCard()->getNumber(),
			        'expiry_month' => $expiryMonth,
			        'expiry_year' => $expiryYear,
			        'cvd' => $this->getCard()->getCvv()
			    );
				$data['card'] = $card;
				$data['payment_method'] = 'card';
			}
			else
			{
				/*
		    		Add parameters we need for a Legato token payment	
		    	*/
				$data['name'] = $this->getCard()->getName();
				$data['payment_method'] = 'token';
			}
        }
		
		return $data;
    }
}
