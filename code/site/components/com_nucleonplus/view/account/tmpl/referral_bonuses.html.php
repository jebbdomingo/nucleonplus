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

    <legend><?= translate('My Referral Bonus') ?></legend>

    <input type="hidden" name="direct_referral" value="<?= $directReferrals ?>" />
    <input type="hidden" name="indirect_referral" value="<?= $indirectReferrals ?>" />
    <table class="table">
        <thead>
            <th>Referral Type</th>
            <th class="text-right">Points</th>
        </thead>
        <tbody>
            <tr>
                <td>Direct Referrals</td>
                <td class="text-right"><?= number_format($directReferrals, 2) ?></td>
            </tr>
            <tr>
                <td>Indirect Referrals</td>
                <td class="text-right"><?= number_format($indirectReferrals, 2) ?></td>
            </tr>
            <tr>
                <td>Total</td>
                <td class="text-right"><strong><?= number_format($totalRewards, 2) ?></strong></td>
            </tr>
        </tbody>
    </table>
    
</fieldset>