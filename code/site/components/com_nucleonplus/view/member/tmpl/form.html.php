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
    'styles' => array('file' => 'admin'),
    'domain' => 'admin'
)); ?>

<? // Add template class to visually enclose the forms ?>
<script>document.documentElement.className += " k-frontend-ui";</script>

<!-- Wrapper -->
<div class="k-wrapper k-js-wrapper">

    <? $account = object('com://admin/nucleonplus.model.accounts')->user_id(object('user')->id)->fetch(); ?>
    <? if (object('request')->getReferrer()->getQuery() === 'view=login' && !$account->sponsor_id): ?>
        <div class="k-alert k-alert--warning k-no-margin">
            <span class="k-icon-warning" aria-hidden="true"></span> Please enter your Sponsor's ID
        </div>
    <? endif ?>

    <!-- Overview -->
    <div class="k-content-wrapper">

        <!-- Content -->
        <div class="k-content k-js-content">

            <!-- Toolbar -->
            <ktml:toolbar type="actionbar">

            <!-- Title when sidebar is invisible -->
            <ktml:toolbar type="titlebar" title="Nucleon Plus" mobile>

            <!-- Component wrapper -->
            <div class="k-component-wrapper">

                <form class="k-component k-js-component k-js-form-controller" method="post">

                    <!-- Container -->
                    <div class="k-container">
                        <!-- Main information -->
                        <div class="k-container__main">
                            <?= import('com://site/nucleonplus.member.member.html', ['member' => $member]) ?>
                        </div>
                    </div>

                </form>

            </div>

        </div>
        
    </div>

</div>
