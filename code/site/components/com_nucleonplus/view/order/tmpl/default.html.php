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

<?
$account = object('com:nucleonplus.model.accounts')->user_id(object('user')->getId())->fetch();
?>

<? // Toolbar ?>
<?= import('com://site/nucleonplus.order.form_manage.html', ['order' => $order]) ?>

<div class="nucleonplus_form_layout">
    <form method="post" class="-koowa-form" action="<?= route('option=com_nucleonplus&view=order'); ?>">

        <div class="row-fluid">

            <div class="span12">

                <legend><?= translate('Nucleon+ Product Package') ?></legend>
                <fieldset class="form-vertical">
                    <label>Choose a Package</label>
                    <div>
                        <input type="hidden" name="account_id" value="<?= $account->id ?>" />
                        <?= helper('listbox.productList', array('name' => 'package_id')) ?>
                    </div>
                </fieldset>

            </div>

        </div>

    </form>
</div>