<?
/**
 * Nucleon+
 *
 * @package     Nucleon+
 * @copyright   Copyright (C) 2016 - 2020 Nucleon + Co.
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

defined('KOOWA') or die('Nooku Framework Not Found');

$show_save   = true;//$order->canPerform('edit');
$button_size = 'btn-small';
?>

<div class="koowa_toolbar">
    <div class="btn-toolbar koowa-toolbar" id="toolbar-rewards">
        <? if ($total_bonus && $show_save): ?>
            <div class="btn-group" id="toolbar-save">
                <a class="toolbar btn <?= $button_size ?> btn-success" data-action="save" href="#">
                    <?= translate('Encash'); ?>
                </a>
            </div>
        <? endif; ?>
        <div class="btn-group" id="toolbar-cancel">
            <a data-novalidate="novalidate" class="toolbar btn <?= $button_size ?>" data-action="cancel" href="#">
                <?= translate('Back') ?>
            </a>
        </div>
    </div>
</div>