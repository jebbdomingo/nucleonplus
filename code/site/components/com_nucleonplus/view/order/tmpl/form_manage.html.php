<?
/**
 * Nucleon+
 *
 * @package     Nucleon+
 * @copyright   Copyright (C) 2016 - 2020 Nucleon + Co.
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

defined('KOOWA') or die('Nooku Framework Not Found');

$show_save   = $order->canPerform('edit');
$button_size = 'btn-small';

if (is_null($order->id))
{
    $save_action         = 'save';
    $save_button_caption = 'Place Order';
}
elseif ($order->order_status == 'awaiting_payment')
{
    $save_action           = 'confirm';
    $save_button_caption   = translate('Confirm Payment');
    $show_cancel           = true;
    $cancel_action         = 'cancelorder';
    $cancel_button_caption = translate('Cancel Order');
}
elseif ($order->order_status == 'shipped')
{
    $save_action         = 'markdelivered';
    $save_button_caption = translate('Order Received');
}
else $show_save = false;
?>

<? // Edit and delete buttons ?>
<div class="koowa_toolbar">
    <div class="btn-toolbar koowa-toolbar" id="toolbar-order">
        <? if ($show_save): ?>
            <div class="btn-group" id="toolbar-<?= $save_action ?>">
                <a class="toolbar btn <?= $button_size ?> btn-success" data-action="<?= $save_action ?>" href="#">
                    <?= translate($save_button_caption); ?>
                </a>
            </div>
            <? if ($show_cancel): ?>
                <div class="btn-group" id="toolbar-<?= $cancel_action ?>">
                    <a class="toolbar btn <?= $button_size ?>" data-action="<?= $cancel_action ?>" href="#">
                        <?= translate($cancel_button_caption) ?>
                    </a>
                </div>
            <? endif; ?>
        <? endif; ?>
        <div class="btn-group" id="toolbar-cancel">
            <a data-novalidate="novalidate" class="toolbar btn <?= $button_size ?>" data-action="cancel" href="#">
                <?= translate('Back') ?>
            </a>
        </div>
    </div>
</div>