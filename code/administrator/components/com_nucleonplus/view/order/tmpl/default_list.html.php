<table class="table table-striped">
    <thead>
        <th>Item</th>
        <th>Quantity</th>
        <th><div class="text-right">Price</div></th>
    </thead>
    <tbody>
        <? if (count($items = $order->getOrderItems()) > 0): ?>
            <? foreach ($items as $item): ?>
                <tr>
                    <td><h6><?= $item->item_name ?></h6></td>
                    <td><h6><?= $item->quantity ?></h6></td>
                    <td><div class="text-right"><h6><strong>&#8369;<?= number_format($item->item_price, 2) ?></strong></h6></div></td>
                </tr>
            <? endforeach ?>
        <? else: ?>
            <tr>
                <td colspan="3">
                    <p class="text-center">Order is empty</p>
                </td>
            </tr>
        <? endif ?>
    </tbody>
</table>
