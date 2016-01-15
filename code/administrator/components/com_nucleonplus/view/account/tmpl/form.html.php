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
<ktml:style src="media://com_nucleonplus/css/admin-edit.css" />

<ktml:module position="toolbar">
    <ktml:toolbar type="actionbar" title="<?= ($account->id) ? object('user.provider')->load($account->user_id)->getName() : 'New Account'; ?>" icon="task-add icon-pencil-2">
</ktml:module>

<div class="deskman_form_layout">
    <form action="<?= route('id='.$account->id) ?>" method="post" class="-koowa-form">

        <div class="row-fluid">
            <div class="span9">
                <legend><?= translate('Details') ?></legend>
                <fieldset class="form-vertical">
                    <div>
                        <label for="note"><?= translate('Note'); ?></label>
                        <div>
                            <?= helper('editor.display', array(
                                'name'    => 'note',
                                'value'    => $account->note,
                                'height' => 100,
                                'options' => array(
                                    'language'         => 'en',
                                    'contentsLanguage' => 'en'
                                )
                            )) ?>
                        </div>
                    </div>
                </fieldset>
            </div>

            <div class="span3">
                <legend><?= translate('Settings') ?></legend>
                <fieldset class="form-vertical">

                    <div class="control-group">
                        <div class="control-label">
                            <label><?= translate('Account No.'); ?></label>
                        </div>
                        <div class="controls">
                            <?= $account->account_number ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="control-label">
                            <label><?= translate('Status'); ?></label>
                        </div>
                        <div class="controls">
                            <?= helper('listbox.optionList', array('name' => 'status', 'selected' => $account->status)) ?>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>

    </form>
</div>