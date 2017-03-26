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
<?= helper('bootstrap.load', array('javascript' => true)); ?>
<?= helper('behavior.keepalive'); ?>
<?= helper('behavior.validator'); ?>

<ktml:style src="media://koowa/com_koowa/css/site.css" />
<ktml:style src="media://com_nucleonplus/css/bootstrap.css" />
<ktml:style src="media://com_nucleonplus/css/site-styles.css" />

<? // Toolbar ?>
<div class="koowa_toolbar">
    <ktml:toolbar type="actionbar" title="COM_NUCLEONPLUS_MEMBER" icon="task-add icon-pencil-2">
</div>

<? // Form ?>
<div class="koowa_form">

    <div class="nucleonplus_form_layout">

        <form method="post" class="form-horizontal -koowa-form">

            <div class="koowa_container">

                <div class="koowa_grid__row">
                    <?= import('com:nucleonplus.member.member.html', ['member' => $member]) ?>
                </div>

            </div>

        </form>
        
    </div>

</div>