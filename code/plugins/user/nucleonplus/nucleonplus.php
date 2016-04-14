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
    /**
     * Hook into onUserBeforeSave user event
     *
     * @return void
     */
    public function onUserBeforeSave($oldUser, $isNew, $newUser)
    {
        $input = JFactory::getApplication()->input;

        // Check activation
        if(!$isNew)
        {
            if(isset($oldUser['activation']) &&
               !empty($oldUser['activation']) &&
               isset($newUser['activation']) &&
               empty($newUser['activation']))
            {
                // Push member to com:qbsync for later sync
                $account  = KObjectManager::getInstance()->getObject('com:nucleonplus.model.accounts')->id($oldUser['id'])->fetch();
                $customer = KObjectManager::getInstance()->getObject('com://admin/nucleonplus.accounting.service.member')->pushMember($account);

                // Attempt to sync member as customer to QBO
                if ($customer->sync() === false)
                {
                    $error = $customer->getStatusMessage();
                    throw new Exception($error ? $error : "Sync Error: Account #{$account->account_number}");
                    return false;
                }

                // Attempt to send success email
                $emailSubject = "Your Nucleon Plus Account has been activated";
                $emailBody    = JText::sprintf(
                    'PLG_USER_NUCLEONPLUS_EMAIL_ACTIVATED_BY_ADMIN_ACTIVATION_BODY',
                    $entity->_name,
                    JUri::root()
                );

                $return = JFactory::getMailer()->sendMail($config->get('mailfrom'), $config->get('fromname'), $entity->_email, $emailSubject, $emailBody);
                if ($return !== true)
                {
                    throw new Exception(JText::_('PLG_USER_NUCLEONPLUS_REGISTRATION_ACTIVATION_NOTIFY_SEND_MAIL_FAILED'));
                    return false;
                }

                return true;
            }
        }
    }
}