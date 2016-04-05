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
     * Display payment instruction panel
     *
     * @param array $config
     *
     * @return string
     */
    public function paymentInstructionPanel(array $config = array())
    {
        $message = $this->paymentInstructionMessage($config);

        $template = '<div class="panel panel-info">';
        $template .= '<div class="panel-heading"><strong>Payment Instruction</strong></div>';
        $template .= "<div class=\"panel-body\">{$message}</div>";
        $template .= '</div>';

        return $template;
    }
    
    /**
     * Display payment instruction alert
     *
     * @param array $config
     *
     * @return string
     */
    public function paymentInstructionAlert(array $config = array())
    {
        $message = $this->paymentInstructionMessage($config);

        $template = '<div class="alert alert-info" role="alert">';
        $template .= '<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> ';
        $template .= $message;
        $template .= '</div>';

        return $template;
    }

    /**
     * Display payment instruction plain text
     *
     * @param array $config
     *
     * @return string
     */
    public function paymentInstructionMessage(array $config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'bank_name'           => 'BDO',
            'bank_account_number' => '9900000001',
            'bank_account_type'   => 'Savings',
            'bank_account_name'   => 'Nucleon + Co.',
            'bank_account_branch' => 'SM Angono',
        ));

        $message = '<p>';
        $message .= "Please deposit your payment (see bank details below) and enter the reference number found in your deposit slip to <strong>\"Deposit slip reference #\"</strong> field of your order";
        $message .= '<p>';
        $message .= "Bank: {$config->bank_name}<br />";
        $message .= "Account Name: {$config->bank_account_name}<br />";
        $message .= "Account Number: {$config->bank_account_number}</strong>";
        $message .= '</p>';
        $message .= '</p>';

        return $message;
    }
}