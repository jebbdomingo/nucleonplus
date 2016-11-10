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
    <ktml:toolbar type="actionbar" title="COM_NUCLEONPLUS_CONFIG" icon="task-edit icon-book">
</ktml:module>

<div class="row-fluid">

    <div class="span12">

        <fieldset class="form-vertical">

            <form method="post" class="-koowa-form">

                <div class="panel panel-default">

                    <div class="panel-heading">
                        <h3 class="panel-title"><?= translate('Configuration') ?></h3>
                    </div>

                    <table class="table">
                        <tbody>
                            <tr>
                                <td><label><strong><?= $config->item ?></strong></label></td>
                                <td>
                                    <?= helper('behavior.calendar', array(
                                        'name'    => 'value',
                                        'id'      => 'value',
                                        'format'  => '%Y-%m-%d',
                                        'value'   => $config->value,
                                        'attribs' => array(
                                            'placeholder' => date('Y-m-d', strtotime("+2 days"))
                                        )
                                    )) ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>

            </form>

        </fieldset>
        
    </div>

</div>