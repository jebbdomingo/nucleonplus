<form method="post" class="-koowa-form">

    <div class="panel panel-default">

        <div class="panel-heading">
            <h3 class="panel-title"><?= translate('Member Details'); ?></h3>
        </div>

        <table class="table">

            <tbody>
                <tr>
                    <td><label><strong><?= translate('Name') ?></strong></label></td>
                    <td>
                        <input name="name" id="name" value="<?= $member->name ?>" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Username') ?></strong></label></td>
                    <td>
                        <input name="username" id="username" value="<?= $member->username ?>" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Email Address') ?></strong></label></td>
                    <td>
                        <input name="email" id="email" value="<?= $member->email ?>" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Name in Check') ?></strong></label></td>
                    <td>
                        <input name="PrintOnCheckName" id="PrintOnCheckName" value="<?= $member->_account_check_name ?>" />
                    </td>
                </tr>
                <? // TODO create a sponsor id update request business logic ?>
                <tr>
                    <td><label><strong><?= translate('Sponsor ID') ?></strong></label></td>
                    <td>
                        <input name="sponsor_id" id="sponsor_id" value="<?= $member->_account_sponsor_id ?>" placeholder="Optional" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Bank Account Number') ?></strong></label></td>
                    <td>
                        <input name="bank_account_number" id="bank_account_number" value="<?= $member->_account_bank_account_number ?>" placeholder="BDO only" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Bank Account Name') ?></strong></label></td>
                    <td>
                        <input name="bank_account_name" id="bank_account_name" value="<?= $member->_account_bank_account_name ?>" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Bank Account Type') ?></strong></label></td>
                    <td>
                        <?= helper('listbox.bankAccountTypes', array(
                            'name'     => 'bank_account_type',
                            'selected' => $member->_account_bank_account_type,
                        )) ?>
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Bank Account Branch') ?></strong></label></td>
                    <td>
                        <input name="bank_account_branch" id="bank_account_branch" value="<?= $member->_account_bank_account_branch ?>" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Phone') ?></strong></label></td>
                    <td>
                        <input name="phone" id="phone" value="<?= $member->_account_phone ?>" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Mobile') ?></strong></label></td>
                    <td>
                        <input name="mobile" id="mobile" value="<?= $member->_account_mobile ?>" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Street') ?></strong></label></td>
                    <td>
                        <input name="street" id="street" value="<?= $member->_account_street ?>" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('City') ?></strong></label></td>
                    <td>
                        <input name="city" id="city" value="<?= $member->_account_city ?>" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('State/Province') ?></strong></label></td>
                    <td>
                        <input name="state" id="state" value="<?= $member->_account_state ?>" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('ZIP/Postal Code') ?></strong></label></td>
                    <td>
                        <input name="postal_code" id="postal_code" value="<?= $member->_account_postal_code ?>" />
                    </td>
                </tr>
            </tbody>

        </table>

    </div>

</form>