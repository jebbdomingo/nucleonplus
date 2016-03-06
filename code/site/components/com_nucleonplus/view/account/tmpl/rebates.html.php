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

<fieldset>

    <legend><?= translate('My Rebates') ?></legend>

    <table class="table">
        <thead>
            <th>Order #</th>
            <th>Product Package</th>
            <th>Slots</th>
            <th class="text-right">Points</th>
        </thead>
        <tbody>
            <? foreach ($rewards as $reward): ?>
                <tr>
                    <td>
                        <input type="hidden" name="rewards[]" value="<?= $reward->id ?>" />
                        <input type="hidden" name="total_rebates[]" value="<?= $reward->total ?>" />
                        <?= $reward->product_id ?>
                    </td>
                    <td><?= $reward->product_name ?></td>
                    <td><?= $reward->slots ?></td>
                    <td class="text-right"><strong><?= number_format($reward->total, 2) ?></strong></td>
                </tr>
            <? endforeach ?>
        </tbody>
    </table>

</fieldset>