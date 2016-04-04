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

<? foreach ($payouts as $payout): ?>
    <tr>
        <td style="text-align: center;">
            <?= helper('grid.checkbox', array('entity' => $payout)) ?>
        </td>
        <td class="deskman_table__title_field">
            <a href="<?= route('view=payout&id='.$payout->id); ?>">
                <?= $payout->id ?>
            </a>
        </td>
        <td>
            <span class="label <?= ($payout->status == 'pending') ? 'label-default' : 'label-info' ?>"><?= ucwords(escape($payout->status)) ?></span>
        </td>
        <td>
            <a href="<?= route('view=account&id='.$payout->account_id); ?>">
                <?= $payout->name ?>
            </a>
        </td>
        <td>
            <a href="<?= route('view=account&id='.$payout->account_id); ?>">
                <?= $payout->account_number ?>
            </a>
        </td>
        <td>
            <?= number_format($payout->amount, 2) ?>
        </td>
        <td>
            <?= helper('date.format', array('date' => $payout->created_on)) ?>
        </td>
    </tr>
<? endforeach; ?>