<?
/**
 * Nucleon+
 *
 * @package     Nucleon+
 * @copyright   Copyright (C) 2016 - 2020 Nucleon + Co.
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

defined('KOOWA') or die('Nooku Framework Not Found');

$show_add    = $order->canPerform('edit');
$button_size = 'btn-small';
?>

<? // Edit and delete buttons ?>
<div class="koowa_toolbar">
    <div class="btn-toolbar koowa-toolbar" id="toolbar-order">
        <? if ($show_add): ?>
            <div class="btn-group" id="toolbar-save">
                <a class="toolbar btn <?= $button_size ?> btn-success" data-action="save" href="#">
                    <?= translate('Place Order'); ?>
                </a>
            </div>
        <? endif; ?>
        <div class="btn-group" id="toolbar-cancel">
            <a data-novalidate="novalidate" class="toolbar btn <?= $button_size ?>" data-action="cancel" href="#">
                <?= translate('Cancel') ?>
            </a>
        </div>
    </div>
</div>