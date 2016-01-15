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

<ktml:style src="media://koowa/com_koowa/css/koowa.css" />
<ktml:style src="media://com_nucleonplus/css/admin-read.css" />

<ktml:module position="toolbar">
    <ktml:toolbar type="actionbar" title="Order No. <?= $order->id; ?>" icon="task-add icon-book">
</ktml:module>

<div class="row-fluid">

    <div class="span6">

        <fieldset class="form-vertical">

            <form method="post" class="-koowa-grid">

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= translate('Details'); ?></h3>
                    </div>
                    <table class="table">
                        <tbody>
                            <tr>
                                <td><label><strong><?= translate('Account No.') ?></strong></label></td>
                                <td><?= $order->account_number ?></td>
                            </tr>
                            <tr>
                                <td><label><strong><?= translate('Order Status'); ?></strong></label></td>
                                <td><span class="label label-<?= ($order->order_status == 'cancelled') ? 'default' : 'info' ?>"><?= ucwords(escape($order->order_status)) ?></span></td>
                            </tr>
                            <tr>
                                <td><label><strong><?= translate('Invoice Status'); ?></strong></label></td>
                                <td><span class="label label-<?= ($order->invoice_status == 'sent') ? 'default' : 'info' ?>"><?= ucwords(escape($order->invoice_status)) ?></span></td>
                            </tr>
                            <tr>
                                <td><label><strong><?= translate('Created On') ?></strong></label></td>
                                <td>
                                    <div><?= helper('date.humanize', array('date' => $order->created_on)) ?></div>
                                    <div><?= $order->created_on ?></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </form>

        </fieldset>
        
    </div>

</div>