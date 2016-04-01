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
        <h3 class="panel-title"><?= translate('Rewards') ?></h3>
    </div>
    <div class="panel-body">
        <table class="table">
            <thead>
                <th>Reward</th>
                <th><div class="pull-right">Points</div></th>
            </thead>
            <tbody>
                <tr>
                    <td>Available Product Rebates</td>
                    <td><div class="pull-right"><?= number_format($total_rebates, 2) ?></div></td>
                </tr>
                <tr>
                    <td>Available Referral Bonuses</td>
                    <td><div class="pull-right"><?= number_format($total_referral_bonus, 2) ?></div></td>
                </tr>
                <tr class="info">
                    <td>Total Available Rewards</td>
                    <td><div class="pull-right"><strong><?= number_format($total_bonus, 2) ?></strong></div></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>