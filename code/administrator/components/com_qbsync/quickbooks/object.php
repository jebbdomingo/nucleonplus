<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @author      Jebb Domingo <https://github.com/jebbdomingo>
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComQbsyncQuickbooksObject extends KObject
{
    /**
     * QuickBooks Context
     *
     * @var mixed
     */
    protected $Context;

    /**
     * QuickBooks realm
     *
     * @var mixed
     */
    protected $realm;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        // Require the library code
        require_once dirname(__FILE__) . '/qbo/QuickBooks.php';

        if (!QuickBooks_Utilities::initialized($config->dsn)) {
            QuickBooks_Utilities::initialize($config->dsn);
        }

        $IntuitAnywhere = new QuickBooks_IPP_IntuitAnywhere(
            $config->dsn,
            $config->encryption_key,
            $config->consumer_key,
            $config->consumer_secret,
            $config->oauth_url,
            $config->oauth_success_url
        );

        if ($IntuitAnywhere->check($config->username, $config->tenant) and 
            $IntuitAnywhere->test($config->username, $config->tenant))
        {
            $quickbooks_is_connected = true;
            $IPP                     = new QuickBooks_IPP($config->dsn);
            $creds                   = $IntuitAnywhere->load($config->username, $config->tenant);

            $IPP->authMode(
                QuickBooks_IPP::AUTHMODE_OAUTH, 
                $config->username, 
                $creds);

            if ($config->sandbox) {
                $IPP->sandbox(true);
            }

            $this->realm   = $creds['qb_realm'];
            $this->Context = $IPP->context();
        }
        else $quickbooks_is_connected = false;
    }

    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KObjectConfig $config Configuration options.
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'consumer_key'      => 'qyprdfgY2z15AAY29sqpYyGOc0OxUh',
            'consumer_secret'   => 'KOGNeRcZwKwO5TcWpTalbg0CYQoFhbTlfP0NUTFQ',
            'sandbox'           => true,
            'oauth_url'         => 'http://joomla.box/quickbooks/docs/partner_platform/example_app_ipp_v3/oauth.php',
            'oauth_success_url' => 'http://joomla.box/quickbooks/docs/partner_platform/example_app_ipp_v3/success.php',
            'dsn'               => 'mysqli://root:root@localhost/example_app_ipp_v3',
            'encryption_key'    => 'bcde1234',
            'username'          => 'DO_NOT_CHANGE_ME',
            'tenant'            => 'b7rhbqgvvi',
        ));

        parent::_initialize($config);
    }
}