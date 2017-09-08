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
        <?= import('com://site/nucleonplus.account.default_sidebar.html'); ?>

        <!-- Content -->
        <div class="k-content k-js-content">

            <!-- Title when sidebar is invisible -->
            <ktml:toolbar type="titlebar" title="<?php echo ucfirst(JFactory::getApplication()->input->get('view')); ?>" mobile>

            <? if (count($payouts) === 0): ?>
                <div class="k-empty-state">
                    <p>It seems like you don't have any payouts yet.</p>
                    <?
                    $account = object('com://site/nucleonplus.model.accounts')->user_id(object('user')->getId())->fetch();
                    $p       = strpos(JURI::root(), '?') ? '&' : '?';
                    $url     = JURI::root() . $p . "sponsor_id={$account->id}";
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
						                <th><?= helper('grid.sort', array('column' => 'id', 'title' => 'Payout #')); ?></th>
						                <th>
                                            <?= helper('grid.sort', array('column' => 'status', 'title' => 'Status')); ?>
                                        </th>
						                <th>
                                            <?= helper('grid.sort', array('column' => 'payout_method', 'title' => 'Encashment method')); ?>
                                        </th>
                                        <th>
                                            <?= helper('grid.sort', array('column' => 'created_on', 'title' => 'Date')); ?>
                                        </th>
						                <th>Amount</th>
					                </tr>
					            </thead>
					            <tbody>
				                    <? foreach ($payouts as $payout): ?>
				                        <tr>
				                            <td><?= $payout->id ?></td>
				                            <td>
				                                <span class="label <?= ($payout->status == 'pending') ? 'label-default' : 'label-info' ?>"><?= ucwords(escape($payout->status)) ?></span>
				                            </td>
				                            <td><?= $payout->payout_method ?></td>
				                            <td><?= helper('date.humanize', array('date' => $payout->created_on)) ?></td>
				                            <td><div class="text-right">&#8369;<?= number_format($payout->amount, 2) ?></div></td>
				                        </tr>
				                    <? endforeach ?>
					            </tbody>
					        </table>
                        </div>

                        <? if (count($payouts)): ?>
                            <div class="k-table-pagination">
                                <?= helper('paginator.pagination') ?>
                            </div><!-- .k-table-pagination -->
                        <? endif; ?>

                    </div>

                </div>

            <? endif ?>

        </div>

    </div>

</div>