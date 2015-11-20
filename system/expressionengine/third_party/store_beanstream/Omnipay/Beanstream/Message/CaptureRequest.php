<?php

namespace Omnipay\Beanstream\Message;

class CaptureRequest extends AbstractRequest
{  
	public function sendData($data)
	{
		$beanstream = new \Beanstream\Gateway(
	    	$this->getActiveMerchantId(), 
	    	$this->getActiveApiKey(), 'www', 'v1'
	    );
	    
		try
		{
		    $result = $beanstream->payments()->complete($data['transaction_id'], $data['amount'], $data['order_number']);
		}
		catch(\Beanstream\Exception $e)
		{
		    $result = array(
		    	'error' => $e->getMessage()
		    );
		}		
        return $this->response = new Response($this, $result);
    }

    public function getData()
    {
		$this->validate('transactionReference');
		return array(
			'transaction_id' => $this->getTransactionReference(),
			'amount' => $this->getAmount(),
			'order_number' => $this->getOrderId()
		);
    }
}
