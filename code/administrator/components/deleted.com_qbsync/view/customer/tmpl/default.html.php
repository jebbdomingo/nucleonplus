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
    <ktml:toolbar type="actionbar" title="<?= ($customer->id) ? 'Customer #' . $customer->id : 'New Customer'; ?>" icon="task-add icon-book">
</ktml:module>

<form method="post" class="-koowa-form">

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?= translate('Customer Details'); ?></h3>
        </div>
        <table class="table">
            <tbody>
                <tr>
                    <td><label><strong><?= translate('Account ID') ?></strong></label></td>
                    <td><input type="text" name="account_id" value="<?= $customer->account_id ?>" /></td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Customer Ref.') ?></strong></label></td>
                    <td><input type="text" name="CustomerRef" value="<?= $customer->CustomerRef ?>" /></td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('DisplayName') ?></strong></label></td>
                    <td><input type="text" name="DisplayName" value="<?= $customer->DisplayName ?>" /></td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Primary Phone') ?></strong></label></td>
                    <td><input type="text" name="PrimaryPhone" value="<?= $customer->PrimaryPhone ?>" /></td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Mobile') ?></strong></label></td>
                    <td><input type="text" name="Mobile" value="<?= $customer->Mobile ?>" /></td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Primary Email Addr') ?></strong></label></td>
                    <td><input type="text" name="PrimaryEmailAddr" value="<?= $customer->PrimaryEmailAddr ?>" /></td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Print On Check Name') ?></strong></label></td>
                    <td><input type="text" name="PrintOnCheckName" value="<?= $customer->PrintOnCheckName ?>" /></td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Line 1') ?></strong></label></td>
                    <td><input type="text" name="Line1" value="<?= $customer->Line1 ?>" /></td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('City') ?></strong></label></td>
                    <td><input type="text" name="City" value="<?= $customer->City ?>" /></td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Postal Code') ?></strong></label></td>
                    <td><input type="text" name="PostalCode" value="<?= $customer->PostalCode ?>" /></td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Country') ?></strong></label></td>
                    <td><input type="text" name="Country" value="<?= $customer->Country ?>" /></td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Synced') ?></strong></label></td>
                    <td>
                        <?= helper('listbox.sync', array(
                            'name'     => 'synced',
                            'selected' => $customer->synced,
                        )) ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</form>