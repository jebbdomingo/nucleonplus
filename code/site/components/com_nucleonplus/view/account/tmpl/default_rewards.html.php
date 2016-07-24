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

<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><?= translate('My Commission') ?></h3>
    </div>
    <div class="panel-body">
        <table class="table">
            <thead>
                <th>&nbsp;</th>
                <th class="text-right">Points</th>
            </thead>
            <tbody>
                <tr>
                    <td>Direct Referral Bonus</td>
                    <td class="text-right"><?= number_format($total_direct_referrals, 2) ?></td>
                </tr>
                <tr>
                    <td>Patronage Bonus</td>
                    <td class="text-right"><?= number_format($total_patronages, 2) ?></td>
                </tr>
                <tr>
                    <td>Unilevel Bonus</td>
                    <td class="text-right"><?= number_format($total_referral_bonus, 2) ?></td>
                </tr>
                <tr class="info">
                    <td>Total Available Points</td>
                    <th class="text-right"><?= number_format($total_bonus, 2) ?></th>
                </tr>
            </tbody>
        </table>

        <? if ($total_bonus): ?>
            <p class="pull-right"><a class="btn btn-primary btn-md" href="<?= route('view=account&layout=rewards&tmpl=koowa') ?>" role="button"><?= translate('Encash') ?></a></p>
        <? endif ?>
    </div>
</div>