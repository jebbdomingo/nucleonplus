<fieldset>
    <div class="k-form-group">
        <label for="sponsor_id"><?= translate('Sponsor ID') ?></label>
        <input class="k-form-control" type="text" id="sponsor_id" name="sponsor_id" value="<?= $member->_account_sponsor_id ?>" />
    </div>
    <div class="k-form-group">
        <label for="name"><?= translate('Name') ?></label>
        <input class="k-form-control" type="text" id="name" name="name" value="<?= $member->name ?>" />
    </div>
    <div class="k-form-group">
        <label for="username"><?= translate('Username') ?></label>
        <input class="k-form-control" type="text" id="username" name="username" value="<?= $member->username ?>" />
    </div>
    <div class="k-form-group">
        <label for="email"><?= translate('Email address') ?></label>
        <input class="k-form-control" type="text" id="email" name="email" value="<?= $member->email ?>" />
    </div>
    <div class="k-form-group">
        <label for="PrintOnCheckName"><?= translate('Name in cheque') ?></label>
        <input class="k-form-control" type="text" id="PrintOnCheckName" name="PrintOnCheckName" value="<?= $member->_account_check_name ?>" />
    </div>
    <div class="k-form-group">
        <label for="bank_name"><?= translate('Bank') ?></label>
        <?= helper('listbox.banks', array(
            'name'     => 'bank_name',
            'selected' => $member->_account_bank_name,
        )) ?>
    </div>
    <div class="k-form-group">
        <label for="bank_account_type"><?= translate('Account type') ?></label>
        <?= helper('listbox.bankAccountTypes', array(
            'name'     => 'bank_account_type',
            'selected' => $member->_account_bank_account_type,
        )) ?>
    </div>
    <div class="k-form-group">
        <label for="bank_account_number"><?= translate('Account number') ?></label>
        <input class="k-form-control" type="text" id="bank_account_number" name="bank_account_number" value="<?= $member->_account_bank_account_number ?>" />
    </div>
    <div class="k-form-group">
        <label for="bank_account_name"><?= translate('Account name') ?></label>
        <input class="k-form-control" type="text" id="bank_account_name" name="bank_account_name" value="<?= $member->_account_bank_account_name ?>" />
    </div>
    <div class="k-form-group">
        <label for="bank_account_branch"><?= translate('Branch of account') ?></label>
        <input class="k-form-control" type="text" id="bank_account_branch" name="bank_account_branch" value="<?= $member->_account_bank_account_branch ?>" />
    </div>
    <div class="k-form-group">
        <label for="phone"><?= translate('Phone') ?></label>
        <input class="k-form-control" type="text" id="phone" name="phone" value="<?= $member->_account_phone ?>" />
    </div>
    <div class="k-form-group">
        <label for="mobile"><?= translate('Mobile') ?></label>
        <input class="k-form-control" type="text" id="mobile" name="mobile" value="<?= $member->_account_mobile ?>" />
    </div>
    <div class="k-form-group">
        <label for="street"><?= translate('Address') ?></label>
        <input class="k-form-control" type="text" id="street" name="street" value="<?= $member->_account_street ?>" />
    </div>
    <div class="k-form-group">
        <label for="city"><?= translate('City') ?></label>
        <?= helper('listbox.cities', array(
            'name'     => 'city',
            'selected' => $member->city_id,
        )) ?>
    </div>
    <div class="k-form-group">
        <label for="postal_code"><?= translate('ZIP/Postal code') ?></label>
        <input class="k-form-control" type="text" id="postal_code" name="postal_code" value="<?= $member->_account_postal_code ?>" />
    </div>
</fieldset>
