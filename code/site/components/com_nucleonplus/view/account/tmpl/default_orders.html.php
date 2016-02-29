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
        <h3 class="panel-title">Orders</h3>
    </div>
    <div class="panel-body">
        <table class="table table-striped">
            <thead>
                <th>Order No.</th>
                <th>Product Package</th>
                <th>Price</th>
                <th>Order Status</th>
                <th>Date</th>
            </thead>
            <tbody>
                <? if (count($purchases = $account->getPurchases()) > 0): ?>
                    <? foreach ($purchases as $order): ?>
                        <tr>
                            <td>
                                <a href="<?= route('view=order&id='.$order->id.'&layout=form&tmpl=koowa') ?>"><?= $order->id ?></a>
                            </td>
                            <td><?= $order->package_name ?></td>
                            <td><?= $order->package_price ?></td>
                            <td><span class="label label-<?= ($order->order_status == 'cancelled') ? 'default' : 'info' ?>"><?= ucwords(escape($order->order_status)) ?></span></td>
                            <td>
                                <div><?= helper('date.humanize', array('date' => $order->created_on)) ?></div>
                                <div><?= $order->created_on ?></div> 
                            </td>
                        </tr>
                    <? endforeach ?>
                <? else: ?>
                    <tr>
                        <td colspan="5">
                            <p class="text-center">No Purchase(s) Yet</p>
                        </td>
                    </tr>
                <? endif ?>
            </tbody>
        </table>
    </div>
</div>