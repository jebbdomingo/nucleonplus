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

<? foreach ($transfers as $transfer): ?>
    <tr>
        <td style="text-align: center;">
            <?= helper('grid.checkbox', array('entity' => $transfer)) ?>
        </td>
        <td class="deskman_table__title_field">
            <a href="<?= route('view=transfer&id='.$transfer->id); ?>">
                <?= $transfer->id ?>
            </a>
        </td>
        <td>
            <span class="label <?= ($transfer->synced == 'no') ? 'label-default' : 'label-info' ?>"><?= ucwords(escape(($transfer->synced == 'no') ? 'No' : 'Yes')) ?></span>
        </td>
        <td><?= $transfer->order_id ?></td>
        <td><?= $transfer->FromAccountRef ?></td>
        <td><?= $transfer->ToAccountRef ?></td>
        <td><?= $transfer->Amount ?></td>
        <td><?= $transfer->PrivateNote ?></td>
    </tr>
<? endforeach; ?>