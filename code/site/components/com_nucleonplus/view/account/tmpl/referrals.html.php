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

<?= helper('behavior.clipboard', array('success_message' => 'Your sponsor link is copied to the clipboard, start sharing!')) ?>

<? // Add template class to visually enclose the forms ?>
<script>document.documentElement.className += " k-frontend-ui";</script>

<!-- Wrapper -->
<div class="k-wrapper k-js-wrapper">

    <!-- Overview -->
    <div class="k-content-wrapper">

        <!-- Sidebar -->
        <?= import('default_sidebar.html'); ?>

        <!-- Content -->
        <div class="k-content k-js-content">

            <!-- Title when sidebar is invisible -->
            <ktml:toolbar type="titlebar" title="Nucleon Plus" mobile>

            <? if (count($account->getDirectReferrals()) === 0): ?>
                <div class="k-empty-state">
                    <p>It seems like you don't have any referrals yet.</p>
                    <?
                    $p   = strpos(JURI::root(), '?') ? '&' : '?';
                    $url = JURI::root() . $p . "sponsor_id={$account->account_number}";
                    ?>
                    <p><a href="#" class="k-button k-button--success k-button--large k-button--clipboard" data-clipboard-text="<?= $url ?>">Share Nucleon + Now!</a></p>
                </div>
            <? else: ?>
                <!-- Component -->
                <div class="k-component-wrapper">
                    <div class="k-table-container">
                        <div class="k-table">
                            <table class="k-js-responsive-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Account No.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <? foreach ($account->getDirectReferrals() as $referral): ?>
                                        <tr>
                                            <td><?= object('user.provider')->load($referral->user_id)->getName() ?></td>
                                            <td><?= $referral->account_number ?></td>
                                        </tr>
                                    <? endforeach ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <? endif ?>

        </div>

    </div>
</div>