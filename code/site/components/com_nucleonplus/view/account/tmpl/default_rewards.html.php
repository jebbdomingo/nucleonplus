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
        <h3 class="panel-title"><?= translate('My Rewards') ?></h3>
    </div>
    <div class="panel-body">
        <table class="table">
            <thead>
                <th>Reward</th>
                <th class="text-right">Points</th>
            </thead>
            <tbody>
                <tr>
                    <td>Available Product Rebates</td>
                    <td class="text-right"><?= number_format($rebates, 2) ?></td>
                </tr>
                <tr>
                    <td>Available Referral Bonuses</td>
                    <td class="text-right"><?= number_format($bonus, 2) ?></td>
                </tr>
                <tr class="info">
                    <td>Total Available Rewards</td>
                    <th class="text-right"><?= number_format($total, 2) ?></th>
                </tr>
            </tbody>
        </table>

        <p class="pull-right"><a class="btn btn-primary btn-md" href="#" role="button">Encash</a></p>
    </div>
</div>