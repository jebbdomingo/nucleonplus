<?
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

defined('KOOWA') or die; ?>

<?= helper('behavior.koowa'); ?>

<ktml:style src="media://koowa/com_koowa/css/koowa.css" />
<ktml:style src="media://com_nucleonplus/css/admin-read.css" />
<ktml:style src="media://com_nucleonplus/css/admin-account-read.css" />

<ktml:module position="toolbar">
    <ktml:toolbar type="actionbar" title="<?= object('user.provider')->load($account->user_id)->getName(); ?>" icon="task-add icon-book">
</ktml:module>

<div class="row-fluid">

    <div class="span3">

        <fieldset class="form-vertical">

            <?= import('com://admin/nucleonplus.account.default_account_summary.html', ['account' => $account]) ?>

        </fieldset>

    </div>

    <div class="span9">

        <fieldset class="form-vertical">

            <form method="post" class="-koowa-form -koowa-grid">
            
                <input type="hidden" name="account_id" value="<?= $account->id ?>" />

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <div class="row-fluid">
                                <div class="span6">
                                    <h3><?= translate('Order') ?> #<?= $order->id ?></h3>
                                </div>
                                <div class="span6">
                                    <div class="text-right">
                                        <?= helper('com://site/nucleonplus.labels.orderStatus', array('value' => $order->order_status)) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <? if ($order->payment_method == ComNucleonplusModelEntityOrder::PAYMENT_METHOD_DRAGONPAY): ?>
                            <div class="well">
                                <h3>Shipping Information</h3>
                                <h4><?= $order->recipient_name ?></h4>
                                <p><?= $order->address ?>, <?= $order->city ?></p>

                                <? if ($order->getCouriers()): ?>
                                <h4>Shipping Cost Breakdown</h4>
                                <table border="1" width="100%">
                                    <thead>
                                        <th>Courier</th>
                                        <th>Weight (gms)</th>
                                        <th>Cost</th>
                                    </thead>
                                    <tbody>
                                        <?
                                        $weight = 0;
                                        $amount = 0;
                                        ?>
                                        <? foreach ($order->getCouriers() as $courier): ?>
                                        <?
                                        $weight += $courier->weight;
                                        $amount += $courier->amount;
                                        ?>
                                            <tr>
                                                <td><strong><?= $courier->name ?></strong></td>
                                                <td><div style="text-align: right"><?= $courier->weight ?></div></td>
                                                <td><div style="text-align: right">&#8369; <?= number_format($courier->amount, 2) ?></div></td>
                                            </tr>
                                        <? endforeach ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td><div style="text-align: right"><?= $weight ?></div></td>
                                            <td><div style="text-align: right">&#8369; <?= number_format($amount, 2) ?></div></td>
                                        </tr></td>
                                        </tr>
                                    </tfoot>
                                </table>
                                <? endif ?>
                            </div>
                        <? endif; ?>
                        
                        <? if ($order->shipping_method == ComNucleonplusModelEntityOrder::SHIPPING_METHOD_XEND): ?>
                            <?= import('tracking.html') ?>
                        <? endif ?>

                        <?= import('default_list.html') ?>

                        <? if ($order->payment_method == ComNucleonplusModelEntityOrder::PAYMENT_METHOD_DRAGONPAY): ?>
                            <div style="text-align: right">
                                Sub-total: &#8369; <?= number_format($order->sub_total, 2) ?>
                            </div>
                            <div style="text-align: right">
                                Shipping: &#8369; <?= number_format($order->shipping_cost, 2) ?>
                            </div>
                            <div style="text-align: right">
                                <?= $order->getPaymentDescription() ?>: &#8369; <?= number_format($order->payment_charge, 2) ?>
                            </div>
                        <? endif ?>

                        <div style="text-align: right">
                            <h4>Total: <strong>&#8369; <?= number_format($order->total, 2) ?></strong></h4>
                        </div>
                    </div>
                </div>

            </form>

        </fieldset>

    </div>

</div>