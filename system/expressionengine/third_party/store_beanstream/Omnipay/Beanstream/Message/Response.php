<?php

namespace Omnipay\Beanstream\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Beanstream Response
 */
class Response extends AbstractResponse
{
    public function isSuccessful()
    {
        parse_str($this->data);
        return ($messageId == 1);
    }

    public function getTransactionReference()
    {
        parse_str($this->data);
        return $trnId;
    }

    public function getMessage()
    {
        if(!$this->isSuccessful())
        {
        	parse_str($this->data);
        	$message = implode('; ', array_filter(explode('<br>', strip_tags(urldecode($messageText), '<br>'))));
        	if($message == 'DECLINE')
        	{
	        	$message = 'Sorry, but your card was declined.';
        	}
            return $message;
        }
    }
}
