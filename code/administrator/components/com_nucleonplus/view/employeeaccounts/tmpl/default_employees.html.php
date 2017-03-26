<?
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */
?>

<? foreach ($employeeaccounts as $employee): ?>
    <tr>
        <td style="text-align: center;">
            <?= helper('grid.checkbox', array('entity' => $employee)) ?>
        </td>
        <td class="deskman_table__title_field">
            <a href="<?= route('view=employee&id='.$employee->id); ?>">
                <?= $employee->id ?>
            </a>
        </td>
        <td>
            <a href="<?= route('view=employee&id='.$employee->id); ?>">
                <?= $employee->_user_name ?>
            </a>
        </td>
        <td><?= $employee->_user_email ?></td>
        <td>
            <?= $employee->DepartmentRef ?>
        </td>
        <td>
            <span class="label <?= ($employee->status == 'terminated') ? 'label-default' : 'label-info' ?>"><?= ucwords(escape($employee->status)) ?></span>
        </td>
        <td>
            <?= helper('date.format', array('date' => $employee->created_on)) ?>
        </td>
        <td>
            <?= $employee->note ?>
        </td>
    </tr>
<? endforeach; ?>