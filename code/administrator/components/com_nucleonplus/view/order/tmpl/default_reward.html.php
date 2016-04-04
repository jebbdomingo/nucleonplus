<form action="<?= route('view=reward&id='.$order->_reward_id) ?>" method="post">

    <input type='hidden' name="_action" value="activate" />

    <div class="well">
        <h3 class="page-header"><?= translate('Reward Details'); ?></h3>
        <table class="table table-condensed">
            <tbody>
                <tr>
                    <td><label><strong><?= translate('Product Package') ?></strong></label></td>
                    <td><?= $order->_reward_product_name ?></td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Slots'); ?></strong></label></td>
                    <td><?= $order->_reward_slots ?></td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Reward Status'); ?></strong></label></td>
                    <td>
                        <span class="label label-<?= ($order->_reward_status == 'pending') ? 'default' : 'info' ?>"><?= ucwords(escape($order->_reward_status)) ?></span>
                    </td>
                </tr>
                <? if ($order->invoice_status == 'paid' && !in_array($order->_reward_status, [
                        'active',
                        'processing',
                        'claimed'
                    ])): ?>
                    <tr>
                        <td><label><strong><?= translate('Action'); ?></strong></label></td>
                        <td>
                            <input class="btn btn-small btn-success" type="submit" value="<?= translate('Activate Reward') ?>" />
                        </td>
                    </tr>
                <? endif; ?>
            </tbody>
        </table>
    </div>

</form>