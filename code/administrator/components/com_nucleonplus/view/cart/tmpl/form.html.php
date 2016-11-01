<table class="table">
    <tbody>
        <tr>
            <td style="width: 10%">Item</td>
            <td style="width: 90%">
                <?= helper('listbox.productList', array(
                    'name' => 'ItemRef'
                )) ?>
            </td>
        </tr>
        <tr>
            <td>Quantity</td>
            <td><input type="text" name="form_quantity" style="width: 30px" value="1" /></td>
        </tr>
    </tbody>
</table>