<?php

namespace Omnipay\Beanstream\Message;

class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
	
	/*
		We use these setters and getters throughout our various Request classes	
	*/
	   
    public function getData(){}
    public function sendData($data){}
    
    public function getAmount()
    {
        return floatval(parent::getAmount());
    }

    public function getPaymentType()
    {
        return ee()->input->post('beanstream_payment_type', 'card');
    }

    public function setPaymentType($data)
    {
        return $this->setParameter('paymentType', $data);
    }      

    public function getLegatoToken()
    {
        return ee()->input->post('legatoToken');
    }

    public function setLegatoToken($data)
    {
        return $this->setParameter('legatoToken', $data);
    }   

    public function getInteracMerchantData()
    {
        return strtolower(ee()->input->get('IDEBIT_MERCHDATA'));
    }

    public function setInteracMerchantData($data)
    {
        return $this->setParameter('interacMerchantData', $data);
    }  

    public function getPreAuth()
    {
        return $this->getParameter('preAuth');
    }

    public function setPreAuth($data)
    {
        return $this->setParameter('preAuth', $data);
    }
    
    public function getLanguage()
    {
        $lang = ee()->input->post('beanstream_language', 'eng');
        return (stripos($lang, 'fr') !== FALSE) ? 'fra' : 'eng';
    }

    public function setLanguage($data)
    {
        return $this->setParameter('language', $data);
    }      

    public function getOrder()
    {
        return $this->getParameter('order');
    }

    public function setOrder($data)
    {
        return $this->setParameter('order', $data);
    }

    public function getOrderId()
    {
        return $this->getParameter('orderId');
    }

    public function setOrderId($data)
    {
        return $this->setParameter('orderId', $data);
    }
        
    public function getActiveMerchantId()
    {
        if($this->getMode() == 'test')
        {
	        return $this->getMerchantIdTest();
        }
        return $this->getMerchantId();
    }

    public function getActiveApiKey()
    {
        if($this->getMode() == 'test')
        {
	        return $this->getApiKeyTest();
        }
        return $this->getApiKey();
    }
    
	public function getMode()
    {
        return $this->getParameter('mode');
    }

    public function setMode($value)
    {
        return $this->setParameter('mode', $value);
    }
    
	public function getApiKey()
    {
        return $this->getParameter('apiKey');
    }

    public function setApiKey($value)
    {
        return $this->setParameter('apiKey', $value);
    }
    
	public function getApiKeyTest()
    {
        return $this->getParameter('apiKeyTest');
    }

    public function setApiKeyTest($value)
    {
        return $this->setParameter('apiKeyTest', $value);
    }
    
    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
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
