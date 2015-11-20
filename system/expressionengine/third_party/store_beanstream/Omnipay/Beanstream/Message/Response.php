<?php

namespace Omnipay\Beanstream\Message;

use Omnipay\Common\Message\AbstractResponse;

class Response extends AbstractResponse
{
    public function isSuccessful()
    {
        return (isset($this->data['approved']) && $this->data['approved'] == 1);
    }

    public function isRedirect()
    {
        return (isset($this->data['merchant_data']));
    }
    
    public function redirect()
    {
	    /*
	    	This will print a self-submitting form to the screen,
	    	creating an automatic redirect to the Interac Online payment process	
	    */
	    exit(urldecode($this->data['contents']));
    }

    public function getTransactionReference()
    {
        return (isset($this->data['id'])) ? $this->data['id'] : false;
    }

    public function getMessage()
    {   
        if(!$this->isSuccessful() && !$this->isRedirect())
        {
        	if(!empty($this->data['error']))
        	{
	        	$message = $this->data['error'];
	        	if($message == 'DECLINE')
	        	{
		        	$message = 'Sorry, but your card was declined.';
	        	}
				return $message;
        	}
        }
    }
}
