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
                <? if ($member->isNew()): ?>
                    <tr>
                        <td><label><strong><?= translate('Sponsor ID') ?></strong></label></td>
                        <td>
                            <input name="sponsor_id" id="sponsor_id" value="<?= $member->sponsor_id ?>" placeholder="Optional" />
                        </td>
                    </tr>
                <? endif ?>
                <tr>
                    <td><label><strong><?= translate('Bank Account Number') ?></strong></label></td>
                    <td>
                        <input name="bank_account_number" id="bank_account_number" value="<?= $member->bank_account_number ?>" placeholder="BDO only" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Bank Account Name') ?></strong></label></td>
                    <td>
                        <input name="bank_account_name" id="bank_account_name" value="<?= $member->bank_account_name ?>" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Bank Account Type') ?></strong></label></td>
                    <td>
                        <input name="bank_account_type" id="bank_account_type" value="<?= $member->bank_account_type ?>" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Bank Account Branch') ?></strong></label></td>
                    <td>
                        <input name="bank_account_branch" id="bank_account_branch" value="<?= $member->bank_account_branch ?>" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Phone') ?></strong></label></td>
                    <td>
                        <input name="phone" id="phone" value="<?= $member->phone ?>" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Mobile') ?></strong></label></td>
                    <td>
                        <input name="mobile" id="mobile" value="<?= $member->mobile ?>" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Street') ?></strong></label></td>
                    <td>
                        <input name="street" id="street" value="<?= $member->street ?>" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('City') ?></strong></label></td>
                    <td>
                        <input name="city" id="city" value="<?= $member->city ?>" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('State/Province') ?></strong></label></td>
                    <td>
                        <input name="state" id="state" value="<?= $member->state ?>" />
                    </td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('ZIP/Postal Code') ?></strong></label></td>
                    <td>
                        <input name="postal_code" id="postal_code" value="<?= $member->postal_code ?>" />
                    </td>
                </tr>
            </tbody>

        </table>

    </div>

</form>