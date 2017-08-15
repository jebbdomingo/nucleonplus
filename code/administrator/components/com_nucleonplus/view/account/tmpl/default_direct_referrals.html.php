<div class="k-table-container">
    <div class="k-table">
        <table class="k-js-responsive-table">
            <thead>
                <th>Name</th>
                <th>Account No.</th>
            </thead>
            <tbody>
                <? if (count($account->getDirectReferrals()) > 0): ?>
                    <? foreach ($account->getDirectReferrals() as $referral): ?>
                        <tr>
                            <td><?= object('user.provider')->load($referral->user_id)->getName() ?></td>
                            <td><?= $referral->id ?></td>
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