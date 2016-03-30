<?
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

defined('KOOWA') or die;

$dr_bonus_points = 0;
$ir_bonus_points = 0;
?>

<fieldset>

    <legend><?= translate('My Referral Bonus') ?></legend>

    <? foreach ($dr_bonuses as $dr_bonus): ?>
        <? $dr_bonus_points += $dr_bonus->points ?>
        <input type="hidden" name="dr_bonuses[]" value="<?= $dr_bonus->id ?>" />
    <? endforeach ?>

    <? foreach ($ir_bonuses as $ir_bonus): ?>
        <? $ir_bonus_points += $ir_bonus->points ?>
        <input type="hidden" name="ir_bonuses[]" value="<?= $ir_bonus->id ?>" />
    <? endforeach ?>

    <table class="table">
        <thead>
            <th>Referral Type</th>
            <th class="text-right">Points</th>
        </thead>
        <tbody>
            <tr>
                <td>Direct Referrals</td>
                <td class="text-right"><?= number_format($dr_bonus_points, 2) ?></td>
            </tr>
            <tr>
                <td>Indirect Referrals</td>
                <td class="text-right"><?= number_format($ir_bonus_points, 2) ?></td>
            </tr>
            <tr>
                <td>Total</td>
                <td class="text-right"><strong><?= number_format(($dr_bonus_points + $ir_bonus_points), 2) ?></strong></td>
            </tr>
        </tbody>
    </table>
    
</fieldset>