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

<?= helper('behavior.clipboard') ?>

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

            <!-- Component -->
            <div class="k-component-wrapper">

                <div class="k-table-container">
                    <div class="k-table">
                        <table class="k-js-responsive-table">
                            <thead>
                                <tr>
                                    <th width="1%"><?= translate('Rewards') ?></th>
                                    <th width="5%" class="k-table-data--right">Points</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Direct referral bonus</td>
                                    <td class="k-table-data--right"><?= number_format($direct_referrals, 2) ?></td>
                                </tr>
                                <tr>
                                    <td>Indirect referral bonus</td>
                                    <td class="k-table-data--right"><?= number_format($indirect_referrals, 2) ?></td>
                                </tr>
                                <tr>
                                    <td>Rebates</td>
                                    <td class="k-table-data--right"><?= number_format($rebates, 2) ?></td>
                                </tr>
                                <tr>
                                    <td><span class="k-table__item--state k-table__item--state-published">Total</span></td>
                                    <td class="k-table-data--right"><span class="k-table__item--state k-table__item--state-published"><?= number_format($total, 2) ?></span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>