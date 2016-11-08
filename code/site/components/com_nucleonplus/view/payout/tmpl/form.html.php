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

<?= helper('behavior.koowa'); ?>
<?= helper('bootstrap.load', array('javascript' => true)); ?>
<?= helper('behavior.keepalive'); ?>
<?= helper('behavior.validator'); ?>

<ktml:style src="media://koowa/com_koowa/css/site.css" />
<ktml:style src="media://com_nucleonplus/css/bootstrap.css" />

<? // Toolbar ?>
<div class="koowa_toolbar">
    <ktml:toolbar type="actionbar">
</div>

<? // Form ?>
<div class="koowa_form">

    <div class="nucleonplus_form_layout">

        <form method="post" class="form-horizontal -koowa-form">
            <div class="koowa_container">

                <div class="koowa_grid__row">

                    <div class="koowa_grid__item two-thirds">

                        <form method="post" class="form-horizontal -koowa-form" action="<?= route('option=com_nucleonplus&view=payout'); ?>">

                            <? foreach ($rebates as $rebate): ?>
                                <input type="hidden" name="rebates[]" value="<?= $rebate->id ?>" />
                            <? endforeach ?>

                            <? foreach ($direct_referrals as $directReferral): ?>
                                <input type="hidden" name="direct_referrals[]" value="<?= $directReferral->id ?>" />
                            <? endforeach ?>

                            <? foreach ($dr_bonuses as $dr_bonus): ?>
                                <input type="hidden" name="dr_bonuses[]" value="<?= $dr_bonus->id ?>" />
                            <? endforeach ?>

                            <? foreach ($ir_bonuses as $ir_bonus): ?>
                                <input type="hidden" name="ir_bonuses[]" value="<?= $ir_bonus->id ?>" />
                            <? endforeach ?>

                            <? foreach ($patronages as $patronage): ?>
                                <input type="hidden" name="patronages[]" value="<?= $patronage->id ?>" />
                            <? endforeach ?>

                            <div class="row-fluid">

                                <br />

                                <div class="span12">

                                    <fieldset>
                                        <legend><?= translate('Bonus') ?></legend>
                                        <table class="table table-striped">
                                            <thead>
                                                <th>Compensation Plan</th>
                                                <th><div class="text-right">Amount</div></th>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Rebates</td>
                                                    <td><div class="text-right"><?= number_format($total_rebates, 2) ?></div></td>
                                                </tr>
                                                <tr>
                                                    <td>Direct referrals bonus</td>
                                                    <td><div class="text-right"><?= number_format($total_direct_referrals, 2) ?></div></td>
                                                </tr>
                                                <tr>
                                                    <td>Unilevel bonus</td>
                                                    <td><div class="text-right"><?= number_format($total_referral_bonus, 2) ?></div></td>
                                                </tr>
                                                <tr>
                                                    <td>Patronage bonus</td>
                                                    <td><div class="text-right"><?= number_format($total_patronages, 2) ?></div></td>
                                                </tr>
                                                <tr class="info">
                                                    <td>&nbsp;</td>
                                                    <td><div class="text-right"><strong>&#8369;<?= number_format($total_bonus, 2) ?></strong></div></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </fieldset>

                                </div>

                                <div class="span12">

                                    <fieldset>

                                        <legend><?= translate('Encashment Method') ?></legend>
                                        <?= helper('listbox.payoutMethods') ?>

                                    </fieldset>

                                </div>

                                <br />

                            </div>
                            
                        </form>

                    </div>

                    <div class="koowa_grid__item one-third">
                        <?= helper('alerts.payoutNotePanel') ?>
                    </div>

                </div>

            </div>

        </form>

    </div>

</div>