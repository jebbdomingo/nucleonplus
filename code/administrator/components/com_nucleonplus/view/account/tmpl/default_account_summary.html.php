<div class="well">
    <h3 class="page-header"><?= translate('Account Summary'); ?></h3>
    <table class="table table-condensed">
        <tbody>
            <tr>
                <td><label><strong><?= translate('Name') ?></strong></label></td>
                <td><?= $account->_name ?></td>
            </tr>
            <tr>
                <td><label><strong><?= translate('Account No.') ?></strong></label></td>
                <td><?= $account->account_number ?></td>
            </tr>
            <tr>
                <td><label><strong><?= translate('Status'); ?></strong></label></td>
                <td><span class="label label-<?= ($account->status == 'closed') ? 'default' : 'info' ?>"><?= ucwords(escape($account->status)) ?></span></td>
            </tr>
            <tr>
                <td><label><strong><?= translate('Registered On') ?></strong></label></td>
                <td>
                    <div><?= helper('date.humanize', array('date' => $account->created_on)) ?></div>
                    <div><?= $account->created_on ?></div>
                </td>
            </tr>
        </tbody>
    </table>
</div>