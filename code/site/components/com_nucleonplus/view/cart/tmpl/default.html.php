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
                                <a class="btn btn-primary btn-sm btn-block" href="<?= route('view=packages') ?>" role="button">
                                    <span class="glyphicon glyphicon-share-alt"></span> Continue shopping
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
                                    <h6><strong>&#8369;<?= number_format($item->_package_price, 2) ?> <span class="text-muted">x</span></strong></h6>
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

                    <h3>Shipping Address</h3>

                    <div class="form-group">
                        <label for="address" class="col-sm-2 control-label">Address</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="address" name="address" placeholder="e.g. Block 1 Lot 2 Nuvali Park ..." size="100%" value="<?= $address ?>" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="city" class="col-sm-2 control-label">City</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="city" name="city" placeholder="e.g. Angono" size="100%" value="<?= $city ?>" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="city" class="col-sm-2 control-label">State/Province</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="state_province" name="state_province" placeholder="e.g. Rizal" size="100%" value="<?= $state_province ?>" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="region" class="col-sm-2 control-label">Region</label>
                        <div class="col-sm-10">
                            <?= helper('listbox.stateProvince', array(
                                'name'     => 'region',
                                'selected' => $region,
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
                                <h6 class="text-right">Added items, calculate sub-total?</h6>
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
                                'entity' => $cart
                            )) ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
