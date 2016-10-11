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
                    <? foreach ($items as $item): ?>
                        <div class="row">
                            <div class="col-sm-2"><img class="img-responsive" src="http://placehold.it/100x70">
                            </div>
                            <div class="col-sm-4">
                                <h4 class="product-name"><strong><?= $item->_package_name ?></strong></h4><h4><small>Product description</small></h4>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-6 text-right">
                                    <h6><strong>&#8369;<?= $item->_package_price ?> <span class="text-muted">x</span> <?= $item->quantity ?></strong></h6>
                                </div>
                            </div>
                        </div>
                        <hr />
                    <? endforeach ?>

                    <div class="row">
                        <div class="text-center">
                            <div class="col-sm-8">
                                <h6 class="text-right">Sub-total</h6>
                            </div>
                            <div class="col-sm-4 text-right">&#8369;<?= $amount ?></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="text-center">
                            <div class="col-sm-8">
                                <h6 class="text-right">Shipping</h6>
                            </div>
                            <div class="col-sm-4 text-right">&#8369;<?= $shipping_cost ?></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="text-center">
                            <div class="col-sm-8">
                                <h6 class="text-right">Dragonpay Charge</h6>
                            </div>
                            <div class="col-sm-4 text-right">&#8369;<?= $cart->getPaymentCharge() ?></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="text-center">
                            <div class="col-sm-8">
                                <h6 class="text-right">Payment Method</h6>
                            </div>
                            <div class="col-sm-4 text-right">
                                <?= $cart->getPaymentChannel() ?>
                            </div>
                        </div>
                    </div>

                    <div class="well">
                        <h3>Ship To:</h3>
                        <?= $address ?>, 
                        <?= $city ?>,
                        <?= $state_province ?>
                    </div>
                </div>


                <div class="panel-footer">
                    <div class="row text-center">
                        <div class="col-xs-9">
                            <h4 class="text-right">Total <strong>&#8369;<?= $total ?></strong></h4>
                        </div>
                        <div class="col-xs-3">
                            <button type="button" class="checkoutAction btn btn-success btn-block">
                                Checkout
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
