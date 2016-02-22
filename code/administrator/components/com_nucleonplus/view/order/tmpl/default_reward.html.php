<form action="<?= route('view=reward&id='.$order->_reward_id) ?>" method="post">

    <input type='hidden' name="_action" value="activate" />

    <div class="well">
        <h3><?= translate('Reward Details'); ?></h3>
        <table class="table">
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
                    <td><label><strong><?= translate('Product Rebate PV'); ?></strong></label></td>
                    <td><?= $order->_reward_prpv ?></td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Direct Referrral PV'); ?></strong></label></td>
                    <td><?= $order->_reward_drpv ?></td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Indirect Referrral PV'); ?></strong></label></td>
                    <td><?= $order->_reward_irpv ?></td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Reward Status'); ?></strong></label></td>
                    <td>
                        <? if ($order->invoice_status == 'paid' && $order->_reward_status <> 'active'): ?>
                            <input class="btn btn-small btn-success" type="submit" value="<?= translate('Activate Reward') ?>" />
                        <? else: ?>
                            <span class="label label-<?= ($order->_reward_status == 'pending') ? 'default' : 'info' ?>"><?= ucwords(escape($order->_reward_status)) ?></span>
                        <? endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</form>