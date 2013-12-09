<?php

class Store_beanstream_ext
{
    public $name = 'Beanstream Payment Gateway for Store';
    public $version = '1.0';
    public $description = 'A custom payment gateway for Expresso Store 2.1+.';
    public $settings_exist = 'n';
    public $docs_url = '';

    public function activate_extension()
    {
        $data = array(
            'class'     => __CLASS__,
            'method'    => 'store_payment_gateways',
            'hook'      => 'store_payment_gateways',
            'priority'  => 10,
            'settings'  => '',
            'version'   => $this->version,
            'enabled'   => 'y'
        );

        ee()->db->insert('extensions', $data);
    }

    /**
     * This hook is called when Store is searching for available payment gateways
     * We will use it to tell Store about our custom gateway
     */
    public function store_payment_gateways($gateways)
    {
        ee()->lang->loadfile('store_beanstream');
        
        // tell Store about our new payment gateway
        // (this must match the name of your gateway in the Omnipay directory)
        $gateways[] = 'Beanstream';

        // tell PHP where to find the gateway classes
        // Store will automatically include your files when they are needed
        $composer = require(PATH_THIRD.'store/autoload.php');
        $composer->add('Omnipay', __DIR__);

        return $gateways;
    }
}
