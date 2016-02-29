<?
$disabled = (is_null($order->id) || in_array($order->order_status, ['awaiting_payment', 'verifying'])) ? false : true;
?>

<form method="post" class="-koowa-form">

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?= translate('Order Details'); ?></h3>
        </div>
        <table class="table">
            <tbody>
                <tr>
                    <td><label><strong><?= translate('Account No.') ?></strong></label></td>
                    <td>
                        <?= helper('listbox.accounts', array(
                            'name'     => 'account_id',
                            'selected' => $order->account_id,
                            'attribs'  => ['disabled' => $disabled],
                        )) ?>
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Product Package') ?></strong></label></td>
                    <td>
                        <?= helper('listbox.packages', array(
                            'name'     => 'package_id',
                            'selected' => $order->package_id,
                            'attribs'  => ['disabled' => $disabled],
                        )) ?>
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Order Status'); ?></strong></label></td>
                    <td>
                        <?= helper('listbox.orderStatus', array(
                            'name'     => 'order_status',
                            'selected' => $order->order_status,
                        )) ?>
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Invoice Status'); ?></strong></label></td>
                    <td>
                        <?= helper('listbox.invoiceStatus', array(
                            'name'     => 'invoice_status',
                            'selected' => $order->invoice_status,
                            'attribs'  => ['disabled' => $disabled],
                        )) ?>
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Payment Method'); ?></strong></label></td>
                    <td>
                        <?= helper('listbox.paymentMethods', array(
                            'name'     => 'payment_method',
                            'selected' => $order->payment_method,
                            'attribs'  => ['disabled' => $disabled],
                        )) ?>
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Shipping Method'); ?></strong></label></td>
                    <td>
                        <?= helper('listbox.shippingMethods', array(
                            'name'     => 'shipping_method',
                            'selected' => $order->shipping_method,
                            'attribs'  => ['disabled' => $disabled],
                        )) ?>
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Payment Reference'); ?></strong></label></td>
                    <td>
                        <? if (in_array($order->invoice_status, ['confirmed', 'paid'])): ?>
                            <?= $order->payment_reference ?>
                        <? else: ?>
                            <textarea name="payment_reference" id="payment_reference"><?= $order->payment_reference ?></textarea>
                        <? endif ?>
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