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
            </tbody>
        </table>
    </div>

</form>