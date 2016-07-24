
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

    <legend><?= translate('Patronage Bonus') ?></legend>

    <table class="table">
        <thead>
            <th>Order #</th>
            <th>Product Pack</th>
            <th class="text-right">Points</th>
        </thead>
        <tbody>
            <? if (count($patronages)): ?>
                <? foreach ($patronages as $patronage): ?>
                    <tr>
                        <td>
                            <input type="hidden" name="patronages[]" value="<?= $patronage->id ?>" />
                            <?= $patronage->product_id ?>
                        </td>
                        <td><?= $patronage->product_name ?></td>
                        <td class="text-right"><strong><?= number_format($patronage->points, 2) ?></strong></td>
                    </tr>
                <? endforeach ?>
            <? else: ?>
                <tr>
                    <td colspan="3" align="center" style="text-align: center;">
                        <?= translate('No record(s) found.') ?>
                    </td>
                </tr>
            <? endif ?>
        </tbody>
    </table>

</fieldset>