<?
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */
?>

<? foreach ($salesreceipts as $salesreceipt): ?>
    <tr>
        <td style="text-align: center;">
            <?= helper('grid.checkbox', array('entity' => $salesreceipt)) ?>
        </td>
        <td class="deskman_table__title_field">
            <a href="<?= route('view=salesreceipt&id='.$salesreceipt->id); ?>">
                <?= $salesreceipt->id ?>
            </a>
        </td>
        <td>
            <span class="label <?= ($salesreceipt->synced == 0) ? 'label-default' : 'label-info' ?>"><?= ucwords(escape(($salesreceipt->synced == 0) ? 'No' : 'Yes')) ?></span>
        </td>
        <td><?= $salesreceipt->DepositToAccountRef ?></td>
        <td><?= $salesreceipt->DocNumber ?></td>
        <td>
            <?= helper('date.format', array('date' => $salesreceipt->TxnDate)) ?>
        </td>
    </tr>
<? endforeach; ?>