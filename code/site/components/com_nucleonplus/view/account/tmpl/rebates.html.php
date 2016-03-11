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
            <th class="text-right">Points</th>
        </thead>
        <tbody>
            <? foreach ($rebates as $rebate): ?>
                <tr>
                    <td>
                        <input type="hidden" name="rebates[]" value="<?= $rebate->id ?>" />
                        <?= $rebate->product_id ?>
                    </td>
                    <td><?= $rebate->product_name ?></td>
                    <td class="text-right"><strong><?= number_format($rebate->points, 2) ?></strong></td>
                </tr>
            <? endforeach ?>
        </tbody>
    </table>

</fieldset>