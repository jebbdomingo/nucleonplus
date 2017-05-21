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

                    <input type="hidden" name="_action" value="updatecart" />
                    <input type="hidden" name="cart_id" value="<?= $cart->id ?>" />
                    <input type="hidden" name="item_id" />

                    <!-- Container -->
                    <div class="k-container">

                        <? if (count($items) === 0): ?>
                            <div class="k-empty-state">
                                <p>It seems like you don't have any items in your cart yet.</p>
                                <p><a href="<?= route('view=products') ?>" class="k-button k-button--success k-button--large">Browse Nucleon + Products Now!</a></p>
                            </div>
                        <? else: ?>
                            <div class="k-card">
                                <div class="k-card__body">
                                    <div class="k-card__header">
                                        Items in your cart
                                    </div>
                                    <div class="k-card__section">
                                        <div class="k-table-container">
                                            <div class="k-table">
                                                <table class="k-js-fixed-table-header k-js-responsive-table">
                                                    <thead>
                                                        <tr>
                                                            <th class="k-sort-desc">Item</th>
                                                            <th width="10%" data-hide="phone,tablet">Price</th>
                                                            <th width="15%" data-hide="phone,tablet,desktop">Quantity</th>
                                                            <th width="5%" data-hide="phone">Amount</th>
                                                            <th width="5%"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <? foreach ($items as $item): ?>
                                                            <tr>
                                                                <td class="k-table-data--ellipsis">
                                                                    <a href="<?= route("view=product&id={$item->_item_id}") ?>"><?= $item->_item_name ?></a>
                                                                </td>
                                                                <td>&#8369; <?= number_format($item->_item_price, 2) ?></td>
                                                                <td class="k-table-data--ellipsis">
                                                                  <input type="text" class="form-control input-sm" size="10" name="quantity[<?= $item->id ?>]" value="<?= $item->quantity ?>">
                                                                </td>
                                                                <td class="k-table-data--nowrap">
                                                                    &#8369; <?= number_format($item->_item_price * $item->quantity, 2) ?>
                                                                </td>
                                                                <td class="k-table-data--nowrap">
                                                                    <button type="button" class="cartItemDeleteAction k-button k-button--default k-button--small" data-id="<?= $item->id ?>">
                                                    <span class="k-icon-trash" aria-hidden="true"></span>
                                                </button>
                                                                </td>
                                                            </tr>
                                                        <? endforeach ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="k-card__footer">
                                        <label><?= translate('Sub-total') ?>: &#8369;<?= number_format($cart->getAmount(), 2) ?></label>
                                    </div>
                                </div>
                            </div>

                            <fieldset class="k-form-block">
                                <div class="k-form-block__header">
                                    Shipping address <small><a href="<?= route('view=member&layout=form&tmpl=koowa') ?>">set default shipping address</a></small>
                                </div>
                                <div class="k-form-block__content">
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
                                </div>
                            </fieldset>                            
                        <? endif ?>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>