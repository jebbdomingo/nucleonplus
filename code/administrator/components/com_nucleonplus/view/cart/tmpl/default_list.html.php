<table class="table table-striped">
    <thead>
        <th style="text-align: center;" width="1">
            <?= helper('grid.checkall')?>
        </th>
        <th>Item</th>
        <th>Price</th>
        <th>Quantity</th>
    </thead>
    <tbody>
        <? if (count($items = $cart->getItems()) > 0): ?>
            <? foreach ($items as $item): ?>
                <tr>
                    <td style="text-align: center;">
                        <?= helper('grid.checkbox', array('entity' => $item)) ?>
                    </td>
                    <td>
                        <h6><?= $item->_item_name ?></h6>
                        <h6><small><?= $item->_item_description ?></small></h6>
                    </td>
                    <td><h6><strong>&#8369;<?= number_format($item->_item_price, 2) ?></strong></h6></td>
                    <td>
                        <input type="text" name="quantity[<?= $item->id ?>]" value="<?= $item->quantity ?>" style="width: 30px" />
                    </td>
                </tr>
            <? endforeach ?>
        <? else: ?>
            <tr>
                <td colspan="4">
                    <p class="text-center">Cart is empty</p>
                </td>
            </tr>
        <? endif ?>
    </tbody>
</table>