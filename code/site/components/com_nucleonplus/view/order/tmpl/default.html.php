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
<?= helper('behavior.orderCancellable'); ?>

<?
if($order->order_status == 'awaiting_payment') {
    $footerAmountSize = 'col-xs-9';
}
else $footerAmountSize = 'col-xs-12';
?>

<ktml:style src="media://koowa/com_koowa/css/koowa.css" />

<div class="row">

    <div class="col-xs-12">

        <fieldset class="form-vertical">

            <form name="orderForm" method="post" class="-koowa-grid">
                <input type="hidden" name="_action" />

                <div class="panel panel-info">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <div class="row">
                                <div class="col-xs-6">
                                    <h5><span class="glyphicon glyphicon-shopping-cart"></span> <?= translate('Order') ?> #<?= $order->id ?></h5>
                                </div>
                                <div class="col-xs-6">
                                    <div class="text-right">
                                        <?= helper('labels.orderStatus', array('value' => $order->order_status)) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel-body">
                        <? if ($order->payment_method == ComNucleonplusModelEntityOrder::PAYMENT_METHOD_DRAGONPAY): ?>
                            <div class="well">
                                <h3>Ship To:</h3>
                                <?= $order->address ?>, 
                                <?= $order->city ?>
                            </div>
                        <? endif; ?>
                        
                        <? foreach ($order->getOrderItems() as $item): ?>
                            <div class="row">
                                <div class="col-xs-2"><img class="img-responsive" src="http://placehold.it/100x70">
                                </div>
                                <div class="col-xs-4">
                                    <h4 class="product-name"><strong><?= $item->item_name ?></strong></h4><h4><small>Item description</small></h4>
                                </div>
                                <div class="col-xs-6 text-right">
                                    <h6><strong>&#8369;<?= number_format($item->item_price, 2) ?> <span class="text-muted">x</span> <?= $item->quantity ?></strong></h6>
                                </div>
                            </div>
                            <hr />
                        <? endforeach ?>

                        <div class="row">
                            <div class="text-center">
                                <div class="col-sm-10">
                                    <h6 class="text-right">Sub-total</h6>
                                </div>
                                <div class="col-sm-2 text-right">&#8369;<?= number_format($order->sub_total, 2) ?></div>
                            </div>
                        </div>
                        <? if ($order->payment_method == ComNucleonplusModelEntityOrder::PAYMENT_METHOD_DRAGONPAY): ?>
                            <div class="row">
                                <div class="text-center">
                                    <div class="col-sm-10">
                                        <h6 class="text-right">Shipping</h6>
                                    </div>
                                    <div class="col-sm-2 text-right">&#8369;<?= $order->shipping_cost ?></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="text-center">
                                    <div class="col-sm-10">
                                        <h6 class="text-right"><?= $order->getPaymentMode() ?></h6>
                                    </div>
                                    <div class="col-sm-2 text-right">&#8369;<?= $order->payment_charge ?></div>
                                </div>
                            </div>
                        <? endif; ?>
                    </div>

                    <div class="panel-footer">
                        <div class="row text-center">
                            <div class="<?= $footerAmountSize ?>">
                                <h4 class="text-right">Total <strong>&#8369;<?= number_format($order->total, 2) ?></strong></h4>
                            </div>
                            <? if ($order->order_status == 'awaiting_payment'): ?>
                            <div class="col-xs-3">
                                <button type="button" class="orderCancelAction btn btn-danger btn-block">
                                    Cancel
                                </button>
                            </div>
                            <? endif ?>
                        </div>
                    </div>
                </div>

            </form>

        </fieldset>

    </div>

</div>