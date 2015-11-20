<?php

namespace Omnipay\Beanstream\Message;

class VoidRequest extends AbstractRequest
{  
	public function sendData($data)
	{
		$beanstream = new \Beanstream\Gateway(
	    	$this->getActiveMerchantId(), 
	    	$this->getActiveApiKey(), 'www', 'v1'
	    );
	    
		try
		{
		    $result = $beanstream->payments()->voidPayment($data['transaction_id'], $data['amount']);
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
		);
    }
}
