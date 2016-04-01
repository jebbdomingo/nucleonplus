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
<ktml:style src="media://com_qbsync/css/bootstrap.css" />

<ktml:module position="toolbar">
    <ktml:toolbar type="actionbar" title="<?= ($salesreceipt->id) ? 'Sales Receipt #' . $salesreceipt->id : 'New Sales Receipt'; ?>" icon="task-add icon-book">
</ktml:module>

<form method="post" class="-koowa-form">

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?= translate('Sales Receipt Details'); ?></h3>
        </div>
        <table class="table">
            <tbody>
                <tr>
                    <td><label><strong><?= translate('Deposit To Account Ref.') ?></strong></label></td>
                    <td><input type="text" name="DepositToAccountRef" value="<?= $salesreceipt->DepositToAccountRef ?>" /></td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Doc Number') ?></strong></label></td>
                    <td><input type="text" name="DocNumber" value="<?= $salesreceipt->DocNumber ?>" /></td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Customer Ref') ?></strong></label></td>
                    <td><input type="text" name="CustomerRef" value="<?= $salesreceipt->CustomerRef ?>" /></td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Date') ?></strong></label></td>
                    <td>
                        <?= helper('date.humanize', array('date' => $salesreceipt->TxnDate)) ?>
                        <?= helper('date.format', array('date' => $salesreceipt->TxnDate)) ?>
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Synced') ?></strong></label></td>
                    <td>
                        <?= helper('listbox.sync', array(
                            'name'     => 'synced',
                            'selected' => $salesreceipt->synced,
                        )) ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</form>