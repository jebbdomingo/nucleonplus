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

<ktml:style src="media://koowa/com_koowa/css/site.css" />
<ktml:style src="media://com_nucleonplus/css/bootstrap.css" />

<? // Toolbar ?>
<?= import('com://site/nucleonplus.order.form_manage.html', ['order' => $order]) ?>

<? // Form ?>
<div class="koowa_form">

    <div class="nucleonplus_form_layout">

        <form method="post" class="form-horizontal -koowa-form">
            <div class="koowa_container">

                <div class="koowa_grid__row">

                    <div class="koowa_grid__item two-thirds">

                        <? // Order form ?>
                        <?= import('com://site/nucleonplus.order.form_order.html', ['order' => $order]) ?>

                        <? // Payment reference form ?>
                        <? if ($order->id): ?>
                            <?= import('com://site/nucleonplus.order.form_payment_reference.html', ['order' => $order]) ?>
                        <? endif ?>

                    </div>

                    <div class="koowa_grid__item one-third">
                        <?= helper('alerts.paymentInstructionPanel') ?>
                    </div>

                </div>

            </div>

        </form>

    </div>

</div>