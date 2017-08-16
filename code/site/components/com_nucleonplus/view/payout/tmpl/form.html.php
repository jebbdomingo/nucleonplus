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

<?= helper('ui.load', array(
    'domain' => 'admin'
)); ?>

<? // Add template class to visually enclose the forms ?>
<script>document.documentElement.className += " k-frontend-ui";</script>

<!-- Wrapper -->
<div class="k-wrapper k-js-wrapper">

    <!-- Overview -->
    <div class="k-content-wrapper">
        
        <!-- Sidebar -->
        <?= import('com://site/nucleonplus.account.default_sidebar.html'); ?>

        <!-- Content -->
        <div class="k-content k-js-content">

            <!-- Title when sidebar is invisible -->
            <ktml:toolbar type="titlebar" title="Nucleon Plus" mobile>

            <!-- Toolbar -->
            <ktml:toolbar type="actionbar">

            <div class="k-alert k-alert--info k-no-margin">
                <span class="k-icon-info" aria-hidden="true"></span> <strong>Reminder</strong> Please note that it will take approximately two (2) banking days after payout request for the payment to reflect on your bank account considering no error on bank information provided.
                Click <a href="<?= route('index.php?option=com_nucleonplus&view=member&tmpl=koowa&layout=form') ?>">here</a> to update your bank details.
            </div>

            <!-- Component -->
            <div class="k-component-wrapper">

                <!-- Component -->
                <form class="k-component k-js-component k-js-form-controller k-js-cart-form" name="k-js-cart-form" action="<?= route('option=com_nucleonplus&view=payout') ?>" method="post">

                    <input type="hidden" name="direct_referrals" value="<?= $direct_referrals ?>">
                    <input type="hidden" name="indirect_referrals" value="<?= $indirect_referrals ?>">
                    <input type="hidden" name="rebates" value="<?= $rebates ?>">

                    <!-- Container -->
                    <div class="k-container">

                        <div class="k-card">
                            <div class="k-card__body">
                                <div class="k-card__header"><?= translate('Rewards') ?></div>
                                <div class="k-card__section">
                                    <div class="k-table-container">
                                        <div class="k-table">
                                            <table class="k-js-fixed-table-header k-js-responsive-table">
                                                <thead>
                                                    <tr>
                                                        <th><?= translate('Compensation Plan') ?></th>
                                                        <th width="15%"><? translate('Amount') ?></th>
                                                        <th width="5%"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="k-table-data--ellipsis">
                                                            <?= translate('Direct referrals') ?>
                                                        </td>
                                                        <td class="k-table-data--nowrap"><?= number_format($direct_referrals, 2) ?></td>
                                                        <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="k-table-data--ellipsis">
                                                            <?= translate('Inirect referrals') ?>
                                                        </td>
                                                        <td class="k-table-data--nowrap"><?= number_format($indirect_referrals, 2) ?></td>
                                                        <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="k-table-data--ellipsis">
                                                            <?= translate('Rebates') ?>
                                                        </td>
                                                        <td class="k-table-data--nowrap"><?= number_format($rebates, 2) ?></td>
                                                        <td>&nbsp;</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="k-card__footer">
                                    <label><?= translate('Total') ?>: &#8369;<?= number_format($total, 2) ?></label>
                                </div>
                            </div>
                        </div>

                        <fieldset class="k-form-block">
                            <div class="k-form-block__header"><?= translate('Options') ?></div>
                            <div class="k-form-block__content">
                                <div class="k-form-group">
                                    <label for="recipient_name"><?= translate('Encashment Method') ?></label>
                                    <?= helper('listbox.payoutMethods') ?>
                                </div>
                            </div>
                        </fieldset>

                        <div class="k-alert k-alert--warning">
                            <span class="k-icon-warning" aria-hidden="true"></span> <strong>Charges</strong> Please be aware that a remittance charge of PHP 15.00 will be deducted from your total payout.
                        </div>

                        <br />

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>
