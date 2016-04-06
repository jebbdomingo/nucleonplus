<?
$disabled = is_null($order->id) ? false : true;
?>

<fieldset>
    <legend><?= translate('Nucleon+ Product Package') ?></legend>

    <? // Product Package ?>
    <div class="control-group">
        <label class="control-label" for="package_id"><?= translate('Choose a Product') ?></label>
        <div class="controls">
            <?= helper('listbox.productList', array(
                'name'     => 'package_id',
                'selected' => ($package_id) ? $package_id : $order->package_id,
                'attribs'  => array(
                    'disabled' => $disabled,
                    'style'    => 'width: 300px'
                )
            )); ?>
        </div>
    </div>

    <? // Payment Method ?>
    <div class="control-group">
        <label class="control-label" for="title"><?= translate('Mode of Payment') ?></label>
        <div class="controls">
            <?= helper('listbox.paymentMethods', array(
                'name'           => 'payment_method',
                'selected'       => $order->payment_method,
                'attribs'        => ['disabled' => $disabled],
                'paymentMethods' => [
                    ['label' => 'Bank Deposit', 'value' => 'deposit']
                ]
            )) ?>
        </div>
    </div>

    <? // Shipping Method ?>
    <div class="control-group">
        <label class="control-label" for="title"><?= translate('Ship via') ?></label>
        <div class="controls">
            <?= helper('listbox.shippingMethods', array(
                'name'            => 'shipping_method',
                'selected'        => $order->shipping_method,
                'attribs'         => ['disabled' => $disabled],
                'shippingMethods' => [
                    ['label' => 'XEND', 'value' => 'xend']
                ]
            )) ?>
        </div>
    </div>

    <? // Order Status ?>
    <div class="control-group">
        <label class="control-label" for="title"><?= translate('Status') ?></label>
        <div class="controls">
            <span class="label label-<?= ($order->order_status == 'cancelled') ? 'default' : 'info' ?>"><?= ucwords(escape($order->order_status)) ?></span>
        </div>
    </div>
</fieldset>