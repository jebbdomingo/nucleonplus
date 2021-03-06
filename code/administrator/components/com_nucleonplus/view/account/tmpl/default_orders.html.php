<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Last 5 Purchases
            <a href="<?= route('view=orders&account_id=' . $account->id) ?>" class="btn btn-default">View All</a>
        </h3>
    </div>
    <div class="panel-body">
        <table class="table table-striped">
            <thead>
                <th>Order #</th>
                <th>Order Status</th>
                <th>Invoice Status</th>
                <th>Date</th>
                <th><div class="text-right">Price</div></th>
            </thead>
            <tbody>
                <? if (count($purchases = $account->getLatestPurchases(5)) > 0): ?>
                    <? foreach ($purchases as $order): ?>
                        <tr>
                            <td>
                                <a href="<?= route('view=order&id='.$order->id) ?>"><?= $order->id ?></a>
                            </td>
                            <td>
                                <span class="label label-<?= ($order->order_status == 'cancelled') ? 'default' : 'info' ?>"><?= ucwords(escape($order->order_status)) ?></span>
                            </td>
                            <td>
                                <span class="label label-<?= ($order->invoice_status == 'sent') ? 'default' : 'info' ?>"><?= ucwords(escape($order->invoice_status)) ?></span>
                            </td>
                            <td>
                                <div><?= helper('date.humanize', array('date' => $order->created_on)) ?></div>
                                <div><?= $order->created_on ?></div> 
                            </td>
                            <td><div class="text-right">&#8369;<?= number_format($order->total, 2) ?></div></td>
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