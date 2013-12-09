<?php

namespace Omnipay\Beanstream;

use Omnipay\Common\AbstractGateway;
use Omnipay\Beanstream\Message\Request;

/**
 * Beanstream Gateway
 *
 * @link https://beanstreamsupport.pbworks.com/w/page/26445764/Transaction%20Processing%20API
 */
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
            'merchantIdCanada' => '',
            'merchantIdUsa' => '',
            'merchantIdInternational' => '',
            'merchantIdTest' => '',
            'hash' => '',
            'debugMode' => false
        );
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

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Beanstream\Message\Request', $parameters);
    }

}
