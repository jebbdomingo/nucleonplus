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

<? foreach ($customers as $customer): ?>
    <tr>
        <td style="text-align: center;">
            <?= helper('grid.checkbox', array('entity' => $customer)) ?>
        </td>
        <td class="deskman_table__title_field">
            <a href="<?= route('view=customer&id='.$customer->id); ?>">
                <?= $customer->id ?>
            </a>
        </td>
        <td><?= ucwords($customer->action) ?></td>
        <td>
            <span class="label <?= ($customer->synced == 'no') ? 'label-default' : 'label-info' ?>"><?= ucwords(escape(($customer->synced == 'no') ? 'No' : 'Yes')) ?></span>
        </td>
        <td><?= $customer->account_id ?></td>
        <td><?= $customer->CustomerRef ?></td>
        <td><?= $customer->DisplayName ?></td>
    </tr>
<? endforeach; ?>