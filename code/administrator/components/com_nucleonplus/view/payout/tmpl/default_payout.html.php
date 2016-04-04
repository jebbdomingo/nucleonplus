<form method="post" class="-koowa-form">

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?= translate('Payout Request Details'); ?></h3>
        </div>
        <table class="table">
            <tbody>
                <tr>
                    <td><label><strong><?= translate('Payout No.'); ?></strong></label></td>
                    <td><?= $payout->id ?></td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Account Number'); ?></strong></label></td>
                    <td><?= $payout->account_number ?></td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Amount'); ?></strong></label></td>
                    <td><?= number_format($payout->amount, 2) ?></td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Status'); ?></strong></label></td>
                    <td><?= $payout->status ?></td>
                </tr>
                <tr>
                    <td><label><strong><?= translate('Date') ?></strong></label></td>
                    <td>
                        <div><?= helper('date.format', array('date' => $payout->created_on)) ?></div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</form>