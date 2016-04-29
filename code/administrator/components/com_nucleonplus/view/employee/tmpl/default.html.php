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

<ktml:module position="toolbar">
    <ktml:toolbar type="actionbar" title="COM_NUCLEONPLUS_SUBMENU_EMPLOYEEACCOUNT" icon="task-add icon-pencil-2">
</ktml:module>

<div class="row-fluid">

    <div class="span12">

        <fieldset class="form-vertical">

            <?= import('com://admin/nucleonplus.employee.employee.html', ['employee' => $employee]) ?>

        </fieldset>
        
    </div>

</div>