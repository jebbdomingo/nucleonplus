<div class="well">
    <h3 class="page-header"><?= translate('Account Details'); ?></h3>
    <table class="table table-condensed">
        <tbody>
            <tr>
                <td><label><strong><?= translate('Member') ?></strong></label></td>
                <td><?= $payout->name ?></td>
            </tr>
            <tr>
                <td><label><strong><?= translate('Account Number'); ?></strong></label></td>
                <td><?= $payout->account_number ?></td>
            </tr>
            <tr>
                <td><label><strong><?= translate('Account Status'); ?></strong></label></td>
                <td>
                    <span class="label label-<?= ($payout->account_status == 'new') ? 'default' : 'info' ?>"><?= ucwords(escape($payout->account_status)) ?></span>
                </td>
            </tr>
            <tr>
                <td><label><strong><?= translate('Member Since'); ?></strong></label></td>
                <td>
                    <?= helper('date.format', array('date' => $payout->account_created_on)) ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>