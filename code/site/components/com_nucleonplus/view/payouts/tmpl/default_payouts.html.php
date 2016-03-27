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

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?= translate('My Payout') ?></h3>
    </div>
    <div class="panel-body" style="padding: 0px">

        <table class="table table-striped footable">
            <thead>
                <th><?= helper('grid.sort', array('column' => 'id', 'title' => 'Payout #')); ?></th>
                <th>Status</th>
                <th>Date</th>
                <th>Amount</th>
            </thead>
            <tbody>
                <? if (count($payouts) > 0): ?>
                    <? foreach ($payouts as $payout): ?>
                        <tr>
                            <td><?= $payout->id ?></td>
                            <td>
                                <span class="label <?= ($payout->status == 'pending') ? 'label-default' : 'label-info' ?>"><?= ucwords(escape($payout->status)) ?></span>
                            </td>
                            <td><?= helper('date.humanize', array('date' => $payout->created_on)) ?></td>
                            <td><?= $payout->amount ?></td>
                        </tr>
                    <? endforeach ?>
                <? else: ?>
                    <tr>
                        <td colspan="4">
                            <p class="text-center">No Purchase(s) Yet</p>
                        </td>
                    </tr>
                <? endif ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4">
                        <?= helper('paginator.pagination') ?>
                    </td>
                </tr>
            </tfoot>
        </table>
        
    </div>
</div>