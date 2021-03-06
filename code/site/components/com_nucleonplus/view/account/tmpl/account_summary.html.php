<div class="well">
    <h4 class="page-header"><span class="glyphicon glyphicon-list" aria-hidden="true"></span> <?= translate('Account Summary'); ?></h4>
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
                <td><label><strong><?= translate('Registered') ?></strong></label></td>
                <td><div><?= helper('date.humanize', array('date' => $account->created_on)) ?></div></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td><a href="<?= route('view=member&layout=form&tmpl=koowa') ?>">Edit Account</a></td>
            </tr>
        </tbody>
    </table>
</div>