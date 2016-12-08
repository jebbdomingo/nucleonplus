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

<ktml:style src="media://koowa/com_koowa/css/koowa.css" />

<?= helper('behavior.koowa'); ?>
<?= helper('bootstrap.load', array('javascript' => true)); ?>
<?= helper('behavior.keepalive'); ?>
<?= helper('behavior.validator'); ?>
<?= helper('behavior.deletable'); ?>
<?= helper('behavior.updatable'); ?>
<?= helper('behavior.confirmable', array('route' => (string) route('view=cart'))); ?>

<div class="row">
    <div class="col-sm-12">
        <form name="cartForm" method="post" action="<?= route('view=cart') ?>" class="form-horizontal">
            <input type="hidden" name="_action" value="updatecart" />
            <input type="hidden" name="cart_id" value="<?= $cart->id ?>" />
            <input type="hidden" name="item_id" />

            <div class="panel panel-info">
                <div class="panel-heading">
                    <div class="panel-title">
                        <div class="row">
                            <div class="col-sm-6">
                                <h5><span class="glyphicon glyphicon-shopping-cart"></span> Shopping Cart</h5>
                            </div>
                            <div class="col-sm-6">
                                <a class="btn btn-primary btn-sm btn-block" href="<?= route('view=products') ?>" role="button">
                                    <span class="glyphicon glyphicon-share-alt"></span> Continue shopping
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <? if (count($items) > 0): ?>
                    <? foreach ($items as $item): ?>
                        <div class="row">
                            <div class="col-sm-2"><img class="img-responsive" src="http://placehold.it/100x70">
                            </div>
                            <div class="col-sm-4">
                                <h4 class="product-name"><strong><?= $item->_item_name ?></strong></h4><h4><small><?= $item->_item_description ?></small></h4>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-6 text-right">
                                    <h6><strong>&#8369;<?= number_format($item->_item_price, 2) ?> <span class="text-muted">x</span></strong></h6>
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-sm" size="10" name="quantity[<?= $item->id ?>]" value="<?= $item->quantity ?>">
                                </div>
                                <div class="col-sm-2">
                                    <button type="button" class="cartItemDeleteAction btn btn-link btn-xs" data-id="<?= $item->id ?>">
                                        <span class="glyphicon glyphicon-trash"> </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <hr />
                    <? endforeach ?>
                    <? else: ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <p class="text-center bg-warning">Your shopping cart is empty</p>
                        </div>
                    </div>
                    <? endif ?>

                    <h3>
                        Shipping Address<br />
                        <small><a href="<?= route('view=member&layout=form&tmpl=koowa') ?>">set default shipping address</a></small>
                    </h3>

                    <div class="form-group">
                        <label for="recipient_name" class="col-sm-2 control-label">Recipient Name</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="recipient_name" name="recipient_name" size="100%" value="<?= $recipient_name ?>" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="recipient_email" class="col-sm-2 control-label">Recipient Email</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="recipient_email" name="recipient_email" size="100%" value="<?= $recipient_email ?>" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address" class="col-sm-2 control-label">Address</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="address" name="address" placeholder="Street, subdivision, baranggay ..." size="100%" value="<?= $address ?>" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="city" class="col-sm-2 control-label">City</label>
                        <div class="col-sm-10">
                            <?= helper('listbox.cities', array(
                                'name'     => 'city',
                                'selected' => $city,
                            )) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="text-center">
                            <div class="col-sm-8">
                                <h6 class="text-right">Payment Method</h6>
                            </div>
                            <div class="col-sm-4 text-right">
                                <?= helper('dragonpay.paymentModes', array(
                                    'selected' => $cart->payment_mode,
                                )) ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="text-center">
                            <div class="col-sm-8">
                                <h6 class="text-right">Added items?</h6>
                            </div>
                            <div class="col-sm-4">
                                <button type="submit" class="cartUpdateAction btn btn-default btn-sm btn-block">
                                    Update cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel-footer">
                    <div class="row text-center">
                        <div class="col-xs-9">
                            <h4 class="text-right">Sub-total <strong>&#8369;<?= number_format($cart->getAmount(), 2) ?></strong></h4>
                        </div>
                        <div class="col-xs-3">
                            <?= helper('dragonpay.confirm', array(
                                'entity'   => $cart,
                                'disabled' => $cart->getAmount() <= 0 ? 'disabled="disabled"' : null
                            )) ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
