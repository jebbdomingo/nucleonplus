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

<?= helper('ui.load', array(
    'styles' => array('file' => 'admin'),
    'domain' => 'admin'
)); ?>

<?= helper('behavior.koowa'); ?>
<?= helper('behavior.keepalive'); ?>
<?= helper('behavior.validator'); ?>
<?= helper('behavior.deletable'); ?>
<?= helper('behavior.updatable'); ?>

<? // Add template class to visually enclose the forms ?>
<script>document.documentElement.className += " k-frontend-ui";</script>

<!-- Wrapper -->
<div class="k-wrapper k-js-wrapper">

    <!-- Overview -->
    <div class="k-content-wrapper">

        <!-- Sidebar -->
        <?= import('com://site/nucleonplus.account.default_sidebar.html'); ?>

        <!-- Content -->
        <div class="k-content k-js-content">

            <!-- Toolbar -->
            <ktml:toolbar type="actionbar">

            <!-- Title when sidebar is invisible -->
            <ktml:toolbar type="titlebar" title="Nucleon Plus" mobile>

            <!-- Component wrapper -->
            <div class="k-component-wrapper">

                <!-- Component -->
                <form class="k-component k-js-component k-js-form-controller k-js-cart-form" name="k-js-cart-form" action="<?= route('view=cart') ?>" method="post">

                    <!-- Container -->
                    <div class="k-container">
                        
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
                                        <div class="col-sm-2">
                                            <img src="<?= JURI::root() . 'images/' . $item->_item_image ?>" alt="<?= $item->_item_name ?>" style="width: 100px" />
                                        </div>
                                        <div class="col-sm-4">
                                            <a href="<?= route("view=product&id=$item->_item_id") ?>"><h4 class="product-name"><strong><?= $item->_item_name ?></strong></h4></a>
                                            <h4><small><?= $item->_item_description ?></small></h4>
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

                                <div class="k-form-group">
                                    <label for="recipient_name"><?= translate('Recipient Name') ?></label>
                                    <input class="k-form-control" type="text" id="recipient_name" name="recipient_name" value="<?= $recipient_name ?>" />
                                </div>

                                <div class="k-form-group">
                                    <label for="address"><?= translate('Address') ?></label>
                                    <input class="k-form-control" type="text" id="address" name="address" value="<?= $address ?>" placeholder="Street, subdivision, baranggay ..." />
                                </div>

                                <div class="k-form-group">
                                    <label for="city"><?= translate('City') ?></label>
                                    <?= helper('listbox.cities', array(
                                        'name'     => 'city',
                                        'selected' => $city,
                                    )) ?>
                                </div>

                                <div class="k-form-group">
                                    <label for="payment_mode"><?= translate('Payment Method') ?></label>
                                    <?= helper('dragonpay.paymentModes', array(
                                        'selected' => $cart->payment_mode,
                                    )) ?>
                                </div>

                                <div class="k-form-group">
                                    <label for="payment_mode"><?= translate('Sub-total') ?>?</label>
                                    <h4 class="text-right"><strong>&#8369;<?= number_format($cart->getAmount(), 2) ?></strong></h4>
                                </div>

                                <div class="k-form-group">
                                    <button type="submit" class="cartUpdateAction btn btn-default">
                                        Update cart
                                    </button>

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

    </div>

</div>