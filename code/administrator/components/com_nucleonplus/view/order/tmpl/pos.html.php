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

<? $locked = (is_null($order->id) || $order->invoice_status <> 'paid') ? false : true; ?>

<ktml:style src="media://koowa/com_koowa/css/koowa.css" />
<ktml:style src="media://com_nucleonplus/css/admin-read.css" />

<ktml:module position="toolbar">
    <ktml:toolbar type="actionbar" title="<?= ($order->id) ? 'Order #' . $order->id : 'New Order'; ?>" icon="task-add icon-book">
</ktml:module>

<div class="row-fluid">

    <div class="span8">

        <fieldset class="form-vertical">

            <form method="post" class="-koowa-form">

                <input type="hidden" name="form_type" value="pos" />

                <div class="panel panel-default">

                    <div class="panel-heading">
                        <h3 class="panel-title"><?= translate('Order Details'); ?></h3>
                    </div>

                    <table class="table">
                        <tbody>
                            <tr>
                                <td><label><strong><?= translate('Account No.') ?></strong></label></td>
                                <td>
                                    <?= helper('listbox.accounts', array(
                                        'name'     => 'account_id',
                                        'selected' => ($account_id) ? $account_id : $order->account_id,
                                        'attribs'  => ['disabled' => $locked],
                                    )) ?>
                                </td>
                            </tr>
                            <tr>
                                <td><label><strong><?= translate('Product Package') ?></strong></label></td>
                                <td>
                                    <?= helper('listbox.packages', array(
                                        'name'     => 'package_id',
                                        'selected' => $order->package_id,
                                        'attribs'  => ['disabled' => $locked],
                                    )) ?>
                                </td>
                            </tr>
                            <tr>
                                <td><label><strong><?= translate('Payment Reference'); ?></strong></label></td>
                                <td>
                                    <textarea name="payment_reference" id="payment_reference" <?= ($locked) ? 'disabled="disabled"' : '' ?>><?= $order->payment_reference ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td><label><strong><?= translate('Note'); ?></strong></label></td>
                                <td>
                                    <textarea name="note" id="note" <?= ($locked) ? 'disabled="disabled"' : '' ?>><?= $order->note ?></textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>

            </form>

        </fieldset>
        
    </div>

    <div class="span4">

        <fieldset class="form-vertical">

            <?= import('com://admin/nucleonplus.order.default_reward.html', ['order' => $order]) ?>

        </fieldset>

    </div>

</div>