<form method="post" class="-koowa-form">

    <div class="panel panel-default">

        <div class="panel-heading">
            <h3 class="panel-title"><?= translate('Employee Details'); ?></h3>
        </div>

        <table class="table">

            <tbody>
                <tr>
                    <td><label><strong><?= translate('Given Name') ?></strong></label></td>
                    <td>
                        <input name="given_name" id="given_name" value="<?= $employee->_employee_given_name ?>" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Family Name') ?></strong></label></td>
                    <td>
                        <input name="family_name" id="family_name" value="<?= $employee->_employee_family_name ?>" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Username') ?></strong></label></td>
                    <td>
                        <input name="username" id="username" value="<?= $employee->username ?>" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Email Address') ?></strong></label></td>
                    <td>
                        <input name="email" id="email" value="<?= $employee->email ?>" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Department Ref.') ?></strong></label></td>
                    <td>
                        <input name="DepartmentRef" id="DepartmentRef" value="<?= $employee->_employee_department_ref ?>" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Bank Account Number') ?></strong></label></td>
                    <td>
                        <input name="bank_account_number" id="bank_account_number" value="<?= $employee->_employee_bank_account_number ?>" placeholder="BDO only" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Bank Account Name') ?></strong></label></td>
                    <td>
                        <input name="bank_account_name" id="bank_account_name" value="<?= $employee->_employee_bank_account_name ?>" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Bank Account Type') ?></strong></label></td>
                    <td>
                        <?= helper('listbox.bankAccountTypes', array(
                            'name'     => 'bank_account_type',
                            'selected' => $employee->_employee_bank_account_type,
                        )) ?>
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Bank Account Branch') ?></strong></label></td>
                    <td>
                        <input name="bank_account_branch" id="bank_account_branch" value="<?= $employee->_employee_bank_account_branch ?>" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Phone') ?></strong></label></td>
                    <td>
                        <input name="phone" id="phone" value="<?= $employee->_employee_phone ?>" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Mobile') ?></strong></label></td>
                    <td>
                        <input name="mobile" id="mobile" value="<?= $employee->_employee_mobile ?>" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Street') ?></strong></label></td>
                    <td>
                        <input name="street" id="street" value="<?= $employee->_employee_street ?>" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('City') ?></strong></label></td>
                    <td>
                        <input name="city" id="city" value="<?= $employee->_employee_city ?>" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('State/Province') ?></strong></label></td>
                    <td>
                        <input name="state" id="state" value="<?= $employee->_employee_state ?>" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('ZIP/Postal Code') ?></strong></label></td>
                    <td>
                        <input name="postal_code" id="postal_code" value="<?= $employee->_employee_postal_code ?>" />
                    </td>
                </tr>
            </tbody>

        </table>

    </div>

</form>