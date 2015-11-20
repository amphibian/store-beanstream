<?php

/*
    This file is part of Beanstream Payment Gateway for Store add-on for ExpressionEngine.

    Beanstream Payment Gateway for Store is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Beanstream Payment Gateway for Store is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    Read the terms of the GNU General Public License
    at <http://www.gnu.org/licenses/>.
    
    Copyright 2015 Derek Hogue
*/

require(PATH_THIRD.'store_beanstream/config.php');

class Store_beanstream_ext
{
    public $name = 'Beanstream Payment Gateway for Store';
    public $version = STORE_BEANSTREAM_VERSION;
    public $description = 'A custom payment gateway for Expresso Store 2.1+.';
    public $settings_exist = 'n';
    public $docs_url = 'https://github.com/amphibian/store-beanstream';

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
        $composer->add('Beanstream', __DIR__.'/vendor/beanstream-php/src');

        return $gateways;
    }
}
