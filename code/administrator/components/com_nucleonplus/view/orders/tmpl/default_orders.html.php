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

<? foreach ($orders as $order): ?>
    <tr>
        <td style="text-align: center;">
            <?= helper('grid.checkbox', array('entity' => $order)) ?>
        </td>
        <td class="deskman_table__title_field">
            <a href="<?= route('view=order&id='.$order->id); ?>">
                <?= $order->id ?>
            </a>
        </td>
        <td>
            <a href="<?= route('view=account&id='.$order->getAccountId()); ?>">
                <?= $order->account_number ?>
            </a>
        </td>
        <td >
            <a href="<?= route('view=order&id='.$order->id); ?>">
                <?= $order->package_name ?>
            </a>
        </td>
        <td><?= $order->package_slots ?></td>
        <td>
            <?= helper('date.humanize', array('date' => $order->created_on)) ?>
            <br />
            <?= $order->created_on ?>
        </td>
        <td>
            <span class="label <?= ($order->order_status == 'cancelled') ? 'label-default' : 'label-info' ?>"><?= ucwords(escape($order->order_status)) ?></span>
        </td>
        <td>
            <span class="label <?= ($order->invoice_status == 'sent') ? 'label-default' : 'label-info' ?>"><?= ucwords(escape($order->invoice_status)) ?></span>
        </td>
        <td><?= ($order->payout) ? $order->payout : '-' ?></td>
    </tr>
<? endforeach; ?>