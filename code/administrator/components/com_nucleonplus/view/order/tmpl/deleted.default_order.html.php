<? $locked = (is_null($order->id) || $order->invoice_status <> 'paid') ? false : true; ?>

<form method="post" class="-koowa-form">

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?= translate('Order Details'); ?></h3>
        </div>
        <table class="table">
            <tbody>
                <tr>
                    <td><label><strong><?= translate('Order No.') ?></strong></label></td>
                    <td><?= $order->id ?></td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Account Number.') ?></strong></label></td>
                    <td>
                        <?= helper('listbox.accounts', array(
                            'name'     => 'account_id',
                            'selected' => ($account_id) ? $account_id : $order->account_id,
                            'attribs'  => ['disabled' => $locked],
                        )) ?>
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Product Package') ?></strong></label></td>
                    <td>
                        <?= helper('listbox.packages', array(
                            'name'     => 'package_id',
                            'selected' => $order->package_id,
                            'attribs'  => ['disabled' => $locked],
                        )) ?>
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Order Status'); ?></strong></label></td>
                    <td>
                        <?= helper('listbox.orderStatus', array(
                            'name'     => 'order_status',
                            'selected' => $order->order_status,
                            'attribs'  => ['disabled' => true],
                        )) ?>
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Invoice Status'); ?></strong></label></td>
                    <td>
                        <?= helper('listbox.invoiceStatus', array(
                            'name'     => 'invoice_status',
                            'selected' => $order->invoice_status,
                            'attribs'  => ['disabled' => true],
                        )) ?>
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Payment Method'); ?></strong></label></td>
                    <td>
                        <?= helper('listbox.paymentMethods', array(
                            'name'           => 'payment_method',
                            'selected'       => $order->payment_method,
                            'attribs'        => ['disabled' => true],
                            'paymentMethods' => [['label' => 'Bank Deposit', 'value' => 'deposit']]
                        )) ?>
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Shipping Method'); ?></strong></label></td>
                    <td>
                        <?= helper('listbox.shippingMethods', array(
                            'name'            => 'shipping_method',
                            'selected'        => $order->shipping_method,
                            'attribs'         => ['disabled' => true],
                            'shippingMethods' => [['label' => 'XEND', 'value' => 'xend']]
                        )) ?>
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Tracking Reference'); ?></strong></label></td>
                    <td>
                        <textarea name="tracking_reference" id="tracking_reference"><?= $order->tracking_reference ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Payment Reference'); ?></strong></label></td>
                    <td>
                        <textarea name="payment_reference" id="payment_reference" <?= ($locked) ? 'disabled="disabled"' : '' ?>><?= $order->payment_reference ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Note'); ?></strong></label></td>
                    <td>
                        <textarea name="note" id="note" <?= ($locked) ? 'disabled="disabled"' : '' ?>><?= $order->note ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Created On') ?></strong></label></td>
                    <td>
                        <div><?= helper('date.humanize', array('date' => $order->created_on)) ?></div>
                        <div><?= $order->created_on ?></div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</form>