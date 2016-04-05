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
                <th>Product Package</th>
                <th>Price</th>
                <th><?= helper('grid.sort', array('column' => 'order_status', 'title' => 'Status')); ?></th>
                <th><?= helper('grid.sort', array('column' => 'created_on', 'title' => 'Date')); ?></th>
                <th>Action</th>
            </thead>
            <tbody>
                <? if (count($orders) > 0): ?>
                    <? foreach ($orders as $order): ?>
                        <tr>
                            <td>
                                <a href="<?= route('view=order&id='.$order->id.'&layout=form&tmpl=koowa') ?>"><?= $order->id ?></a>
                            </td>
                            <td><?= $order->package_name ?></td>
                            <td><?= $order->package_price ?></td>
                            <td>
                                <span class="label label-<?= ($order->order_status == 'cancelled') ? 'default' : 'info' ?>"><?= ucwords(escape($order->order_status)) ?></span>
                            </td>
                            <td>
                                <?= helper('date.humanize', array('date' => $order->created_on)) ?>
                            </td>
                            <td>
                                <? if ($order->order_status == 'shipped'): ?>
                                    <a href="<?= route('view=order&id=' . $order->id . '&layout=form&tmpl=koowa') ?>" class="btn btn-primary btn-xs" role="button"><?= translate('Confirm Receipt of Order') ?></a>
                                <? elseif ($order->order_status == 'awaiting_payment'): ?>
                                    <a href="<?= route('view=order&id=' . $order->id . '&layout=form&tmpl=koowa') ?>" class="btn btn-primary btn-xs" role="button"><?= translate('Confirm your payment') ?></a>
                                <? endif ?>
                            </td>
                        </tr>
                    <? endforeach ?>
                <? else: ?>
                    <tr>
                        <td colspan="6">
                            <p class="text-center">No Purchase(s) Yet</p>
                        </td>
                    </tr>
                <? endif ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6">
                        <?= helper('paginator.pagination') ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>