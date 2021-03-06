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
        <h3 class="panel-title"><?= translate('My Orders') ?></h3>
    </div>
    <div class="panel-body" style="padding: 0px">
        <table class="table table-striped footable">
            <thead>
                <th><?= helper('grid.sort', array('column' => 'id', 'title' => 'Order #')); ?></th>
                <th><?= helper('grid.sort', array('column' => 'created_on', 'title' => 'Date')); ?></th>
                <th><?= helper('grid.sort', array('column' => 'order_status', 'title' => 'Status')); ?></th>
                <th>
                    <div class="text-right">
                        <?= helper('grid.sort', array('column' => 'total', 'title' => 'Total')); ?>
                    </div>
                </th>
            </thead>
            <tbody>
                <? if (count($orders) > 0): ?>
                    <? foreach ($orders as $order): ?>
                        <tr>
                            <td><a href="<?= route('view=order&id='.$order->id) ?>"><?= $order->id ?></a></td>
                            <td><?= helper('date.humanize', array('date' => $order->created_on)) ?></td>
                            <td><?= helper('labels.orderStatus', array('value' => $order->order_status)) ?></td>
                            <td>
                                <div class="text-right">&#8369;<?= number_format($order->total, 2) ?></div>
                            </td>
                        </tr>
                    <? endforeach ?>
                <? else: ?>
                    <tr>
                        <td colspan="4">
                            <p class="text-center">No Purchases Yet</p>
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