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
<?= helper('behavior.orderDeliverable'); ?>

<?
if (in_array($order->order_status, array(ComNucleonplusModelEntityOrder::STATUS_PENDING, ComNucleonplusModelEntityOrder::STATUS_PAYMENT, ComNucleonplusModelEntityOrder::STATUS_SHIPPED)))
{
    $footerAmountSize = 'col-xs-9';
}
else $footerAmountSize = 'col-xs-12';
?>

<ktml:style src="media://koowa/com_koowa/css/koowa.css" />
<ktml:style src="media://com_nucleonplus/css/order-steps.css" />

<div class="row">

    <div class="col-xs-12">

        <?= helper('ordertimeline.timeline', array('state' => $order->order_status)); ?>

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
                                <h3>Shipping Address</h3>
                                <h5><?= $order->recipient_name ?></h5>
                                <p><?= $order->address ?>, <?= $order->city ?></p>
                            </div>
                        <? endif; ?>
                        
                        <? foreach ($order->getOrderItems() as $item): ?>
                            <div class="row">
                                <div class="col-xs-2">
                                    <img src="<?= JURI::root() . 'images/' . $item->item_image ?>" alt="<?= $item->item_name ?>" style="width: 100px" />
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
                                        <h6 class="text-right"><?= $order->getPaymentDescription() ?></h6>
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
                            <? if (in_array($order->order_status, array(ComNucleonplusModelEntityOrder::STATUS_PENDING,ComNucleonplusModelEntityOrder::STATUS_PAYMENT))): ?>
                                <div class="col-xs-3">
                                    <form action="<?= route('view=order') ?>" method="post">
                                        <button type="button" role="button" class="orderCancelAction btn btn-danger btn-block">
                                            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                            Cancel Order
                                        </button>
                                    </form>
                                </div>
                            <? elseif ($order->order_status == ComNucleonplusModelEntityOrder::STATUS_SHIPPED): ?>
                                <div class="col-xs-3">
                                    <form action="<?= route('view=order') ?>" method="post">
                                        <button type="button" role="button" class="orderDeliverable btn btn-success btn-block">
                                            <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>
                                            Order Received
                                        </button>
                                    </form>
                                </div>
                            <? endif ?>
                        </div>
                    </div>
                </div>

            </form>

        </fieldset>

    </div>

</div>