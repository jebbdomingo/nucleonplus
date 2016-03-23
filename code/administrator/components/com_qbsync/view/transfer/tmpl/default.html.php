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
    <ktml:toolbar type="actionbar" title="<?= ($transfer->id) ? 'Transfer #' . $transfer->id : 'New Transfer'; ?>" icon="task-add icon-book">
</ktml:module>

<form method="post" class="-koowa-form">

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?= translate('Transfer Details'); ?></h3>
        </div>
        <table class="table">
            <tbody>
                <tr>
                    <td><label><strong><?= translate('From Account Ref.') ?></strong></label></td>
                    <td><input type="text" name="FromAccountRef" value="<?= $transfer->FromAccountRef ?>" /></td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('To Account Ref.') ?></strong></label></td>
                    <td><input type="text" name="ToAccountRef" value="<?= $transfer->ToAccountRef ?>" /></td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Amount') ?></strong></label></td>
                    <td><input type="text" name="Amount" value="<?= $transfer->Amount ?>" /></td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Private Note') ?></strong></label></td>
                    <td><input type="text" name="PrivateNote" value="<?= $transfer->PrivateNote ?>" /></td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Synced') ?></strong></label></td>
                    <td>
                        <?= helper('listbox.sync', array(
                            'name'     => 'synced',
                            'selected' => $transfer->synced,
                        )) ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</form>