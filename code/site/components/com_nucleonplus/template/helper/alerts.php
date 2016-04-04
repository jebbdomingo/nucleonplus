<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusTemplateHelperAlerts extends KTemplateHelperAbstract
{
    /**
     * Display payment instruction
     *
     * @param array $config
     *
     * @return string
     */
    public function paymentInstruction(array $config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'bank_name'           => 'BDO',
            'bank_account_number' => '9900000001',
            'bank_account_type'   => 'Savings',
            'bank_account_name'   => 'Nucleon + Co.',
            'bank_account_branch' => 'SM Angono',
        ));

        $message = '<div class="panel panel-info">';
        $message .= '<div class="panel-heading"><strong>Payment Instruction</strong></div>';
        $message .= "<div class=\"panel-body\"><p>Please deposit your payment (see bank details below) and enter the reference number found in your deposit slip to <strong>\"Deposit slip reference #\" field</strong></p>";
        $message .= "<p><strong>{$config->bank_name}<br />{$config->bank_account_name}<br />{$config->bank_account_number}</strong></p></div>";
        $message .= '</div>';

        return $message;
    }
}