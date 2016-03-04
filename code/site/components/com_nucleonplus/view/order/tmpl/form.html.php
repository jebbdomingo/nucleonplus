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

                <? // Order form ?>
                <?= import('com://site/nucleonplus.order.form_order.html', ['order' => $order]) ?>

                <? // Payment reference form ?>
                <? if ($order->id): ?>
                    <?= import('com://site/nucleonplus.order.form_payment_reference.html', ['order' => $order]) ?>
                <? endif ?>

            </div>

        </div>

    </form>

</div>