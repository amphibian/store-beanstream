<?php

namespace Omnipay\Beanstream;

use Omnipay\Common\AbstractGateway;
use Omnipay\Beanstream\Message\Request;

class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'Beanstream';
    }

    public function getDefaultParameters()
    {
        return array(
            'mode' => array('test', 'production'),
            'merchantId' => '',
            'apiKey' => '',
            'merchantIdTest' => '',
            'apiKeyTest' => '',
            'interacUrl' => $this->getInteracUrl()
        );
    }
    
    /*
    	Setters and Getters	
    */

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

    public function getInteracUrl()
    {
    	return ee()->config->item('site_url').QUERY_MARKER.'ACT='.
    		ee()->functions->insert_action_ids(
    			ee()->functions->fetch_action_id('Store_beanstream', 'process_interac_response')
    		);
    }

    public function setInteracUrl($value)
    {
        return false;
    }
    
    public function getPreAuth()
    {
        return $this->getParameter('preAuth');
    }

    public function setPreAuth($data)
    {
        return $this->setParameter('preAuth', $data);
    }
    
    /*
    	Methods declaring capability and mapping to Messsage classes	
    */
    
    public function authorize(array $parameters = array())
    {
        $this->setPreAuth('y');
        return $this->purchase($parameters);
    }

    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Beanstream\Message\CaptureRequest', $parameters);
    }

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Beanstream\Message\PurchaseRequest', $parameters);
    }

    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Beanstream\Message\CompletePurchaseRequest', $parameters);
    }
	
    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Beanstream\Message\RefundRequest', $parameters);
    }

    public function void(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Beanstream\Message\VoidRequest', $parameters);
    }

}
