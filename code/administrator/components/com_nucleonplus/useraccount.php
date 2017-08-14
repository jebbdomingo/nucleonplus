<?php

class ComNucleonplusUseraccount extends KObject
{
    /**
     * User
     *
     * @var KUserInterface
     */
    protected $_user;

    /**
     * Account model
     *
     * @var KModelInterface
     */
    protected $_account_model;

    /**
     * Account
     *
     * @var KModelEntityInterface
     */
    protected $_account;

    /**
     * Constructor
     *
     * @param KObjectConfig $config An optional KObjectConfig object with configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $user          = $this->getObject('user');
        $account_model = $this->getObject('com://admin/nucleonplus.model.accounts');

        $account = $account_model->user_id($user->getId())->fetch();
        $account_model->account_number($account->account_number);

        $this->_user          = $user;
        $this->_account_model = $account_model;
        $this->_account       = $account;
    }

    /**
     * Get user
     *
     * @return KUser
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * Get account model
     *
     * @return [KModelInterface
     */
    public function getAccountModel()
    {
        return $this->_account_model;
    }

    /**
     * Get account
     *
     * @return KModelEntityInterface
     */
    public function getAccount()
    {
        return $this->_account;
    }
}