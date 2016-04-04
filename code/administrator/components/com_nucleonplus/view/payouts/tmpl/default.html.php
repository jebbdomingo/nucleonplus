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

<?= helper('bootstrap.load', array('javascript' => true)); ?>
<?= helper('behavior.koowa'); ?>

<ktml:style src="media://koowa/com_koowa/css/admin.css" />

<ktml:module position="submenu">
    <ktml:toolbar type="menubar">
</ktml:module>

<ktml:module position="toolbar">
    <ktml:toolbar type="actionbar" title="COM_NUCLEONPLUS_SUBMENU_PAYOUTS" icon="task icon-stack">
</ktml:module>

<div class="nucleonplus-container">
    <div class="nucleonplus_admin_list_grid">
        <form action="" method="get" class="-koowa-grid">
            <div class="scopebar">
                <div class="scopebar-group last hidden-tablet hidden-phone">
                    <?php echo helper('listbox.payoutStatusFilter', array('active_status' => parameters()->status)); ?>
                </div>
                <div class="scopebar-search">
                    <?= helper('grid.search', array('submit_on_clear' => true, 'placeholder' => 'Account Number or Member\'s Name')) ?>
                </div>
            </div>
            <div class="nucleonplus_table_container">
                <table class="table table-striped footable">
                    <thead>
                        <tr>
                            <th style="text-align: center;" width="1">
                                <?= helper('grid.checkall')?>
                            </th>
                            <th class="nucleonplus_table__title_field">
                                <?= helper('grid.sort', array('column' => 'id', 'title' => 'ID')); ?>
                            </th>
                            <th>
                                <?= helper('grid.sort', array('column' => 'status', 'title' => 'Status')); ?>
                            </th>
                            <th>
                                <?= helper('grid.sort', array('column' => 'name', 'title' => 'Member')); ?>
                            </th>
                            <th>
                                <?= helper('grid.sort', array('column' => 'account_number', 'title' => 'Account Number')); ?>
                            </th>
                            <th>
                                <?= helper('grid.sort', array('column' => 'amount', 'title' => 'Amount')); ?>
                            </th>
                            <th>
                                <?= helper('grid.sort', array('column' => 'created_on', 'title' => 'Date')); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <? if (count($payouts)): ?>
                            <?= import('default_payouts.html', ['payouts' => $payouts]) ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" align="center" style="text-align: center;">
                                    <?= translate('No payout request') ?>
                                </td>
                            </tr>
                        <? endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7">
                                <?= helper('paginator.pagination') ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </form>
    </div>
</div>