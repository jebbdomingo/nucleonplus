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

<? $account = object('com:nucleonplus.model.accounts')->user_id(object('user')->getId())->fetch(); ?>

<?= helper('bootstrap.load', array('javascript' => true)); ?>
<?= helper('behavior.koowa'); ?>
<?= helper('behavior.modal'); ?>
<?= helper('behavior.keepalive'); ?>
<?= helper('behavior.validator'); ?>

<ktml:style src="media://koowa/com_koowa/css/koowa.css" />

<? // Toolbar ?>
<?= import('com://site/nucleonplus.order.form_manage.html', ['order' => $order]) ?>

<div class="koowa_form">

    <form method="post" class="form-horizontal -koowa-form" action="<?= route('option=com_nucleonplus&view=order'); ?>">

        <input type="hidden" name="account_id" value="<?= $account->id ?>" />

        <div class="row-fluid">

            <div class="span8">

                <fieldset>
                    <legend><?= translate('Nucleon+ Product Package') ?></legend>

                    <? // Product Package ?>
                    <div class="control-group">
                        <label class="control-label" for="package_id"><?= translate('Choose a Package') ?></label>
                        <div class="controls">
                            <?= helper('listbox.productList', array(
                                'name'     => 'package_id',
                                'selected' => $order->package_id)) ?>
                        </div>
                    </div>

                    <? // Payment Method ?>
                    <div class="control-group">
                        <label class="control-label" for="title"><?= translate('Payment Method') ?></label>
                        <div class="controls">
                            <?= helper('listbox.paymentMethods', array(
                                'name'     => 'payment_method',
                                'selected' => $order->payment_method
                            )) ?>
                        </div>
                    </div>

                    <? // Shipping Method ?>
                    <div class="control-group">
                        <label class="control-label" for="title"><?= translate('Shipping Method') ?></label>
                        <div class="controls">
                            <?= helper('listbox.shippingMethods', array(
                                'name'     => 'shipping_method',
                                'selected' => $order->shipping_method
                            )) ?>
                        </div>
                    </div>
                </fieldset>

            </div>

        </div>

    </form>

</div>