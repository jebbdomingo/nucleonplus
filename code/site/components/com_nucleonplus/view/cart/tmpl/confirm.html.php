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
<?= helper('bootstrap.load', array('javascript' => true)); ?>
<?= helper('behavior.keepalive'); ?>
<?= helper('behavior.validator'); ?>
<?= helper('behavior.checkout', array('route' => (string) route('view=order'))); ?>

<div class="row">
    <div class="col-sm-12">
        <form name="cartForm" method="post" action="<?= route('view=order') ?>" class="form-horizontal">
            <input type="hidden" name="_action" value="add" />
            <input type="hidden" name="cart_id" value="<?= $cart->id ?>" />

            <div class="panel panel-info">
                <div class="panel-heading">
                    <div class="panel-title">
                        <div class="row">
                            <div class="col-sm-6">
                                <h5><span class="glyphicon glyphicon-shopping-cart"></span> Confirm your order</h5>
                            </div>
                            <div class="col-sm-6">
                                <a class="btn btn-primary btn-sm btn-block" href="<?= route('view=cart&layout=&account_id=') ?>" role="button">
                                    <span class="glyphicon glyphicon-chevron-left"></span> Back
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="well">
                        <h3>Ship To:</h3>
                        <?= $address ?>, 
                        <?= $cart->city ?>
                    </div>
                    
                    <? foreach ($items as $item): ?>
                        <div class="row">
                            <div class="col-sm-2"><img class="img-responsive" src="http://placehold.it/100x70">
                            </div>
                            <div class="col-sm-4">
                                <h4 class="product-name"><strong><?= $item->_item_name ?></strong></h4><h4><small><?= $item->_item_description ?></small></h4>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-6 text-right">
                                    <h6><strong>&#8369;<?= number_format($item->_item_price, 2) ?> <span class="text-muted">x</span> <?= $item->quantity ?></strong></h6>
                                </div>
                            </div>
                        </div>
                        <hr />
                    <? endforeach ?>

                    <div class="row">
                        <div class="text-center">
                            <div class="col-sm-10">
                                <h6 class="text-right">Sub-total</h6>
                            </div>
                            <div class="col-sm-2 text-right">&#8369;<?= $amount ?></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="text-center">
                            <div class="col-sm-10">
                                <h6 class="text-right">Shipping</h6>
                            </div>
                            <div class="col-sm-2 text-right">&#8369;<?= $shipping_cost ?></div>
                        </div>
                    </div>
                    <? if (JFactory::getApplication()->getCfg('debug')): ?>
                        <div class="row">
                            <div class="text-center">
                                <div class="col-sm-10">
                                    <h6 class="text-right">Weight</h6>
                                </div>
                                <div class="col-sm-2 text-right"><?= $cart->getWeight() ?></div>
                            </div>
                        </div>
                    <? endif; ?>
                    <div class="row">
                        <div class="text-center">
                            <div class="col-sm-10">
                                <h6 class="text-right"><?= $cart->getPaymentMode() ?></h6>
                            </div>
                            <div class="col-sm-2 text-right">&#8369;<?= number_format($cart->getPaymentCharge(), 2) ?></div>
                        </div>
                    </div>
                </div>


                <div class="panel-footer">
                    <div class="row text-center">
                        <div class="col-xs-9">
                            <h4 class="text-right">Total <strong>&#8369;<?= number_format($total, 2) ?></strong></h4>
                        </div>
                        <div class="col-xs-3">
                            <button type="button" class="cartCheckoutAction btn btn-success btn-block">
                                Checkout
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
