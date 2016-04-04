<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Direct Referrals</h3>
    </div>
    <div class="panel-body">
        <table class="table table-striped">
            <thead>
                <th>Name</th>
                <th>Account No.</th>
            </thead>
            <tbody>
                <? if (count($account->getDirectReferrals()) > 0): ?>
                    <? foreach ($account->getDirectReferrals() as $referral): ?>
                        <tr>
                            <td><?= object('user.provider')->load($referral->user_id)->getName() ?></td>
                            <td><?= $referral->account_number ?></td>
                        </tr>
                    <? endforeach ?>
                <? else: ?>
                    <tr>
                        <td colspan="2">
                            <p class="text-center">No Direct Referrals</p>
                        </td>
                    </tr>
                <? endif ?>
            </tbody>
        </table>
    </div>
</div>