<? $account = object('com://site/nucleonplus.model.accounts')->user_id(object('user')->getId())->fetch(); ?>

<div class="k-sidebar-left k-js-sidebar-left">

    <div class="k-sidebar-item">
        <ul class="k-navigation">
            <li class="<?= parameters()->view === 'account' && parameters()->layout === 'default' ? 'k-is-active' : null ?>">
                <a href="<?= route('view=account&layout=') ?>">
                    Account
                </a>
            </li>
        </ul>
    </div>

    <div class="k-sidebar-item k-js-sidebar-toggle-item">
        <div class="k-sidebar-item__header">
            <?= translate('Quick Navigation') ?>
        </div>
        <ul class="k-list">
            <li class="<?= parameters()->layout === 'rewards' ? 'k-is-active' : null ?>">
                <a href="<?= route('view=account&layout=rewards') ?>">
                    <span class="k-icon-star" aria-hidden="true"></span>
                    Rewards
                </a>
            </li>
            <li class="<?= parameters()->layout === 'referrals' ? 'k-is-active' : null ?>">
                <a href="<?= route('view=account&layout=referrals') ?>">
                    <span class="k-icon-heart" aria-hidden="true"></span>
                    Referrals
                </a>
            </li>
            <li class="<?= parameters()->view === 'orders' ? 'k-is-active' : null ?>">
                <a href="<?= route('view=orders') ?>">
                    <span class="k-icon-box" aria-hidden="true"></span>
                    Orders
                </a>
            </li>
            <li class="<?= parameters()->view === 'payouts' ? 'k-is-active' : null ?>">
                <a href="<?= route('view=payouts') ?>">
                    <span class="k-icon-dollar" aria-hidden="true"></span>
                    Payouts
                </a>
            </li>
        </ul>
    </div>

    <div class="k-sidebar-item">    
        <div class="k-sidebar-item__header"><?= translate('Account Summary') ?></div>
        <div class="k-sidebar-item__content">
            <?= import('com://site/nucleonplus.account.account_summary.html', ['account' => $account]) ?>
        </div>
    </div>

</div>
