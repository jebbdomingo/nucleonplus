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

<?= helper('bootstrap.load', array('javascript' => true)); ?>
<?= helper('behavior.koowa'); ?>
<?= helper('behavior.keepalive'); ?>
<?= helper('behavior.validator'); ?>

<ktml:style src="media://koowa/com_koowa/css/koowa.css" />
<ktml:style src="media://com_nucleonplus/css/admin-purchase.css" />

<ktml:module position="toolbar">
    <ktml:toolbar type="actionbar" title="Product Purchase" icon="task-add icon-pencil-2">
</ktml:module>

<div class="nucleonplus_form_layout">
    <form method="post" class="-koowa-form" action="<?= route('option=com_nucleonplus&view=purchase'); ?>">

        <div class="row-fluid">

            <div class="span12">

                <legend><?= translate('Product Package') ?></legend>
                <fieldset class="form-vertical">
                    <label>Choose a Package</label>
                    <div>
                        <input type="hidden" name="account_id" value="<?= parameters()->id ?>" />
                        <?= helper('listbox.productList', array('name' => 'package_id')) ?>
                    </div>
                </fieldset>

            </div>

        </div>

    </form>
</div>