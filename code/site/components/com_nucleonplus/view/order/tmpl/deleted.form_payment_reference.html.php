<fieldset>
    <legend><?= translate('Payment Reference') ?></legend>

    <? // Product Package ?>
    <div class="control-group">
        <label class="control-label" for="payment_reference"><?= translate('Deposit slip reference #') ?></label>
        <div class="controls">
            <textarea <?= ($order->order_status <> 'awaiting_payment') ? 'disabled="disabled"' : '' ?> name="payment_reference"><?= escape($order->payment_reference) ?></textarea>
        </div>
    </div>
</fieldset>