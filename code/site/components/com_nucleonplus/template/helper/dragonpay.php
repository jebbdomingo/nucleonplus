<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

/**
 * Status Template Helper
 *
  * @package Nucleon Plus
 */
class ComNucleonplusTemplateHelperDragonpay extends ComKoowaTemplateHelperListbox
{
    /**
     * Generates payment modes list box
     * 
     * @param array $config [optional]
     * 
     * @return html
     */
    public function paymentModes(array $config = array())
    {
        $config = new KObjectConfig($config);

        $options = array(
            array('label' => 'Online Banking (Funds Transfer)', 'value' => 1),
            array('label' => 'Over-the-Counter Banking and ATM', 'value' => 2),
            array('label' => 'Over-the-Counter non-Bank', 'value' => 4)
        );

        $config->append(array(
            'name'     => 'payment_mode',
            'select2'  => true,
            'selected' => null,
            'options'  => $options,
            'filter'   => array(),
            'attribs'  => array(
                'style' => 'width: 100%'
            )
        ));

        if($config->select2 && !$config->searchable)
        {
            $config->append(array(
                'select2_options' => array(
                    'options' => array(
                        'minimumResultsForSearch' => 'Infinity'
                    )
                )
            ));
        }

        return parent::optionlist($config);
    }

    protected function _isProcessorActive($processor)
    {
        return $processor->status == 'A';
    }

    protected function _isAmountAllowed($processor, $amount)
    {
        return $amount >= $processor->minAmount && $amount < $processor->maxAmount;
    }

    public function confirm(array $config = array())
    {
        $config = new KObjectConfig($config);

        $shippingCost  = $config->entity->getShippingFee();
        $paymentCharge = $config->entity->getPaymentCharge();

        $btnState = $config->disabled ? 'k-button--default' : 'k-button--success';
        $disabled = $config->disabled ? 'disabled="disabled"' : null;
        $btnText  = 'Confirm';

        $html = '<button type="button" ' . $disabled . ' class="cartConfirmCheckoutAction k-button ' . $btnState . ' btn-block">
                    ' . $btnText . '
                 </button>';

        return $html;
    }

    // /**
    //  * Generates payment method list box
    //  * 
    //  * @param array $config [optional]
    //  * 
    //  * @return html
    //  */
    // public function paymentChannels(array $config = array())
    // {
    //     $config = new KObjectConfig($config);

    //     $client = new SoapClient('http://test.dragonpay.ph/DragonPayWebService/MerchantService.asmx?wsdl');
    //     $result = $client->GetAvailableProcessors(array(
    //         'merchantId' => 'NUCLEON',
    //         'password'   => 'eRGTsJ73DcjkL2J',
    //         'amount'     => $config->amount
    //     ));

    //     //var_dump($result->GetAvailableProcessorsResult->ProcessorInfo);die;
        
    //     foreach ($result->GetAvailableProcessorsResult->ProcessorInfo as $processor)
    //     {
    //         // Business rules to add payment options
    //         $allowed = $this->_isProcessorActive($processor) && $this->_isAmountAllowed($processor, $config->amount);

    //         if ($allowed) {
    //             $options[] = array('label' => $processor->longName, 'value' => "{$processor->procId}|{$processor->type}|{$processor->longName}");
    //         }
    //     }

    //     $config->append(array(
    //         'name'     => 'payment_channel',
    //         'selected' => null,
    //         'options'  => $options,
    //         'filter'   => array(),
    //         'attribs'  => array(
    //             'style' => 'width: 100%'
    //         )
    //     ));

    //     return parent::optionlist($config);
    // }
}
