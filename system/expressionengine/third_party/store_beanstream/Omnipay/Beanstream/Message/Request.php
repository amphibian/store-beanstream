<?php

namespace Omnipay\Beanstream\Message;

use Omnipay\Common\Message\AbstractRequest;

/**
 * Beanstream  Request
 */
class Request extends AbstractRequest
{
    public function getMerchantId()
    {
        if($this->getMode() == 'test')
        {
	        return $this->getMerchantIdTest();
        }
        
        switch($this->getCard()->getCountry())
        {
	        case 'CA':
	        	return $this->getMerchantIdCanada();
	        	break;
	        case 'US':
	        	return $this->getMerchantIdUsa();
	        	break;
	        default:
	        	return $this->getMerchantIdInternational();
	        	break;
        }
    }

    public function send()
    {
        return $this->sendData($this->getData());
    }
    
	public function sendData($data){
	    
	    if($this->getDebugMode())
	    {
		    echo '<pre><code>';
		    echo '<h1>Data Array</h1>';
		    print_r($data);
		    echo '<h1>Request String</h1>';
		    echo $this->getEndpoint().'?'.http_build_query($data);
		    echo '</code></pre>';
		    exit();
	    }
	    
	    $httpRequest = $this->httpClient->createRequest(
            'POST',
            $this->getEndpoint(),
            null,
            $data
        );
        
        $httpResponse = $httpRequest->send();
        return $this->response = new Response($this, $httpResponse->getBody());
    }

    public function getData()
    {
        $this->getCard()->validate();

        $data = array();
        $data['requestType'] = 'BACKEND';
        $data['trnType'] = 'P'; 
		$data['merchant_id'] = $this->getMerchantId();
		
		$data['trnOrderNumber'] = substr($this->getDescription(), strpos($this->getDescription(), "#")+1);
		$data['trnAmount'] = $this->getAmount();
		$data['trnCardOwner'] = $this->getCard()->getName();
		$data['trnCardNumber'] = $this->getCard()->getNumber();
		$data['trnExpMonth'] = str_pad($this->getCard()->getExpiryMonth(), 2, '0', STR_PAD_LEFT);
		$data['trnExpYear'] = substr($this->getCard()->getExpiryYear(), 2, 2);
		$data['trnCardCvd'] = $this->getCard()->getCvv();
		
		$data['ordName'] = $this->getCard()->getName();
		$data['ordEmailAddress'] = $this->getCard()->getEmail();
		if($this->getCard()->getPhone())
		{
			$data['ordPhoneNumber'] = $this->getCard()->getPhone();			
		}
		$data['ordAddress1'] = $this->getCard()->getAddress1();
		if($this->getCard()->getAddress2())
		{
			$data['ordAddress2'] = $this->getCard()->getAddress2();			
		}
		$data['ordCity'] = $this->getCard()->getCity();
		$data['ordProvince'] = ($this->getCard()->getState()) ? $this->getCard()->getState() : '--';
		$data['ordPostalCode'] = $this->getCard()->getPostcode();
		$data['ordCountry'] = $this->getCard()->getCountry();
		$data['customerIP'] = ee()->input->ip_address();

		if($this->getHash())
		{
			$data['hashValue'] = $this->hashRequest($data);
		}
		
        return $data;
    }
            
    public function hashRequest($data)
    {
	    // Beanstream doesn't like properly-encoded email addresses?
	    // $hash = str_replace('%40', '@', http_build_query($data)).$this->getHash();
	    $hash = http_build_query($data).$this->getHash();
	    return sha1($hash);
    }
    
    public function getEndpoint()
    {
	    return 'https://www.beanstream.com/scripts/process_transaction.asp';
    }    
    
	public function getMode()
    {
        return $this->getParameter('mode');
    }

    public function setMode($value)
    {
        return $this->setParameter('mode', $value);
    }

	public function getDebugMode()
    {
        return $this->getParameter('debugMode');
    }

    public function setDebugMode($value)
    {
        return $this->setParameter('debugMode', $value);
    }  

	public function getHash()
    {
        return $this->getParameter('hash');
    }

    public function setHash($value)
    {
        return $this->setParameter('hash', $value);
    }
    
     public function getMerchantIdCanada()
    {
        return $this->getParameter('merchantIdCanada');
    }

    public function setMerchantIdCanada($value)
    {
        return $this->setParameter('merchantIdCanada', $value);
    }    

    public function getMerchantIdUsa()
    {
        return $this->getParameter('merchantIdUsa');
    }

    public function setMerchantIdUsa($value)
    {
        return $this->setParameter('merchantIdUsa', $value);
    }
    
    public function getMerchantIdInternational()
    {
        return $this->getParameter('merchantIdInternational');
    }

    public function setMerchantIdInternational($value)
    {
        return $this->setParameter('merchantIdInternational', $value);
    }      
    
    public function getMerchantIdTest()
    {
        return $this->getParameter('merchantIdTest');
    }

    public function setMerchantIdTest($value)
    {
        return $this->setParameter('merchantIdTest', $value);
    }
}
