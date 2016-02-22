<form method="post" class="-koowa-form">

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?= translate('Order Details'); ?></h3>
        </div>
        <table class="table">
            <tbody>
                <tr>
                    <td><label><strong><?= translate('Account No.') ?></strong></label></td>
                    <td><?= $order->account_number ?></td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Order Status'); ?></strong></label></td>
                    <td><span class="label label-<?= ($order->order_status == 'cancelled') ? 'default' : 'info' ?>"><?= ucwords(escape($order->order_status)) ?></span></td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Invoice Status'); ?></strong></label></td>
                    <td><span class="label label-<?= ($order->invoice_status == 'sent') ? 'default' : 'info' ?>"><?= ucwords(escape($order->invoice_status)) ?></span></td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Payment Reference'); ?></strong></label></td>
                    <td>
                        <? if ($order->invoice_status <> 'paid'): ?>
                            <textarea name="payment_reference" id="payment_reference"></textarea>
                        <? else: ?>
                            <?= $order->payment_reference ?>
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