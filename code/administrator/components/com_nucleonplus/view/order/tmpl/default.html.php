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

<?= helper('behavior.koowa'); ?>

<ktml:style src="media://koowa/com_koowa/css/koowa.css" />
<ktml:style src="media://com_nucleonplus/css/admin-read.css" />

<ktml:module position="toolbar">
    <ktml:toolbar type="actionbar" title="<?= ($order->id) ? 'Order #' . $order->id : 'New Order'; ?>" icon="task-add icon-book">
</ktml:module>

<div class="row-fluid">

    <div class="span8">

        <fieldset class="form-vertical">

            <?= import('com://admin/nucleonplus.order.default_order.html', ['order' => $order]) ?>

        </fieldset>
        
    </div>

    <div class="span4">

        <fieldset class="form-vertical">

            <?= import('com://admin/nucleonplus.order.default_reward.html', ['order' => $order]) ?>

        </fieldset>
        
    </div>

</div>