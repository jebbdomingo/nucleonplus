<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class PlgUserNucleonplus extends JPlugin
{
    const _USER_GROUP_REGISTERED_ = 2;

    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }

    /**
     * Hook into onUserBeforeSave user event
     *
     * @param [type] $[name] [<description>]
     *
     * @return boolean
     */
    public function onUserBeforeSave($oldUser, $isNew, $newUser)
    {
        // Existing user (Update)
        if(!$isNew)
        {
            // On user activation
            if(isset($oldUser['activation']) &&
               !empty($oldUser['activation']) &&
               isset($oldUser['requireReset']) &&
               $oldUser['requireReset'] == 0 &&
               isset($newUser['activation']) &&
               empty($newUser['activation']))
            {
                if ($customer = $this->_syncAccount($oldUser))
                {
                    $account  = KObjectManager::getInstance()->getObject('com://admin/nucleonplus.model.accounts')->id($oldUser['id'])->fetch();
                    $account->CustomerRef = $customer->CustomerRef;
                    $account->activate();
                    $account->save();

                    // Attempt to send success email
                    $emailSubject = "Your Nucleon Plus Account has been activated";
                    $emailBody    = JText::sprintf(
                        'PLG_NUCLEONPLUS_EMAIL_ACTIVATION_BODY',
                        $account->_name,
                        JUri::root()
                    );

                    $config = JFactory::getConfig();
                    $email  = JFactory::getMailer()->sendMail($config->get('mailfrom'), $config->get('fromname'), $account->_email, $emailSubject, $emailBody);
                    if ($email !== true)
                    {
                        throw new Exception(JText::sprintf('PLG_NUCLEONPLUS_REGISTRATION_ACTIVATION_NOTIFY_SEND_MAIL_FAILED'));
                        return false;
                    }
                }
            }
        }

        // New user (Registration)
        if ($isNew)
        {
            // Validate Sponsor ID field
            return $this->_validateSponsorId($newUser);
        }

        return true;
    }

    /**
     * Hook into onUserAfterSave user event
     *
     * @return void
     */
    public function onUserAfterSave($user, $isNew, $success, $msg)
    {
        // Registration succeeded
        if ($isNew && $success && in_array(self::_USER_GROUP_REGISTERED_, $user['groups']))
        {
            // Create corresponding Nucleon Plus Account upon user registration
            if ($account = $this->_createAccount($user))
            {
                // Push member to accounting service for later sync
                KObjectManager::getInstance()->getObject('com://admin/nucleonplus.accounting.service.member')->pushMember($account);
            }
        }
    }

    /**
     * Validate Sponsor ID
     *
     * @param array $user
     *
     * @throws Exception on error
     *
     * @return boolean
     */
    protected function _validateSponsorId($user)
    {
        if (isset($user['sponsor_id']) && $user['sponsor_id'])
        {
            $sponsor = KObjectManager::getInstance()->getObject('com://admin/nucleonplus.model.accounts')->account_number($user['sponsor_id'])->fetch();

            if (count($sponsor) == 0) {
                throw new Exception(JText::sprintf('PLG_NUCLEONPLUS_REGISTRATION_SAVE_FAILED', 'Invalid Sponsor ID'));
                return false;
            }
        }

        return true;
    }

    /**
     * Sync account to QBO
     *
     * @param array  $user
     * @param string $action [optional]
     *
     * @throws Exception on error
     *
     * @return object Customer
     */
    protected function _syncAccount($user, $action = 'add')
    {
        $account  = KObjectManager::getInstance()->getObject('com://admin/nucleonplus.model.accounts')->id($user['id'])->fetch();
        $customer = KObjectManager::getInstance()->getObject('com://admin/qbsync.model.customers')
            ->account_id($user['id'])
            ->action($action)
            ->fetch()
        ;

        // Attempt to sync member as customer to QBO
        if ($customer->sync() === false)
        {
            $error = $customer->getStatusMessage();
            throw new Exception($error ? $error : "Sync Error: Account #{$account->account_number}");
            return false;
        }

        return $customer;
    }

    /**
     * Create Nucleon Plus Account
     *
     * @param array $user
     *
     * @throws Exception on error
     *
     * @return KModelEntityInterface
     */
    protected function _createAccount($user)
    {
        // Create account
        $model   = KObjectManager::getInstance()->getObject('com://admin/nucleonplus.model.accounts');
        $account = $model->create(array(
            'status'              => 'new',
            'id'                  => $user['id'],
            'user_id'             => $user['id'],
            'PrintOnCheckName'    => $user['name'],
            'sponsor_id'          => $user['sponsor_id'],
            'bank_account_number' => $user['bank_account_number'],
            'bank_account_name'   => $user['bank_account_name'],
            'bank_account_type'   => $user['bank_account_type'],
            'bank_account_branch' => $user['bank_account_branch'],
            'phone'               => $user['phone'],
            'mobile'              => $user['mobile'],
            'street'              => $user['street'],
            'city'                => $user['city'],
            'state'               => $user['state'],
            'postal_code'         => $user['postal_code'],
        ));
        
        if ($account->save() === false)
        {
            $error = $account->getStatusMessage();
            throw new Exception($error ? $error : 'Create Account Failed');
            return false;
        }

        // Fetch newly created account to get joined tables
        $account = KObjectManager::getInstance()->getObject('com://admin/nucleonplus.model.accounts')->id($account->id)->fetch();

        return $account;
    }
}