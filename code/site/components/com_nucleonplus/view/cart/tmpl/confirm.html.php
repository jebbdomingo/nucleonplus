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
                <form class="k-component k-js-component k-js-form-controller k-js-cart-form" name="k-js-cart-form" action="<?= route('view=order') ?>" method="post">
                    <input type="hidden" name="_action" value="add" />
                    <input type="hidden" name="cart_id" value="<?= $cart->id ?>" />

                    <!-- Container -->
                    <div class="k-container">
                        <h3>Recipient</h3>
                        <dl>
                            <dt>Name:</dt>
                            <dd><?= $recipient_name ?></dd>
                            <dt>Address:</dt>
                            <dd><?= $address ?>, <?= $cart->city ?></dd>
                        </dl>
                                
                        <div class="k-card">
                            <div class="k-card__body">
                                <div class="k-card__header">
                                    Content
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
                                                              <?= $item->quantity ?>
                                                            </td>
                                                            <td class="k-table-data--nowrap">
                                                                &#8369; <?= number_format($item->_item_price * $item->quantity, 2) ?>
                                                            </td>
                                                        </tr>
                                                    <? endforeach ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="k-card__footer">
                                    <label><?= translate('Sub-total') ?>: &#8369;<?= $amount ?></label><br />
                                    <label><?= translate('Shipping') ?>: &#8369;<?= $shipping_cost ?></label><br />
                                    <? if (JFactory::getApplication()->getCfg('debug')): ?>
                                        <label><?= translate('Weight (gms)') ?>: <?= $cart->getWeight() ?></label><br />
                                    <? endif ?>
                                    <label><?= translate($cart->getPaymentDescription()) ?>: &#8369;<?= number_format($cart->getPaymentCharge(), 2) ?></label><br />
                                    <label><?= translate('Total') ?>: &#8369;<?= number_format($total, 2) ?></label><br />
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
