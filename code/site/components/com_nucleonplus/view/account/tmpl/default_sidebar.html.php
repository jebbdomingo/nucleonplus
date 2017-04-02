<div class="k-sidebar-left">

    <div class="k-sidebar-item">
        <ul class="k-navigation">
            <li class="<?= parameters()->layout === 'rewards' ? 'k-is-active' : null ?>">
                <a href="<?= route('view=account&layout=rewards') ?>">
                    Account
                </a>
            </li>
            <li class="<?= parameters()->view === 'cart' ? 'k-is-active' : null ?>">
                <a href="<?= route('view=cart') ?>">
                    Cart
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

    <div class="k-sidebar-item__header"><?= translate('Sponsor Link') ?></div>
    <div class="k-sidebar-item__content">
        <div class="k-form-group">
            <div class="k-input-group k-input-group--small k-input-group--public-url">
                <label class="k-input-group__addon" for="public_url">URL</label>
                <?
                $p   = strpos(JURI::root(), '?') ? '&' : '?';
                $url = JURI::root() . $p . "sponsor_id={$account->account_number}";
                ?>
                <input type="text" id="public_url" class="k-form-control" value="<?= $url ?>" />
                <span class="k-input-group__button">
                    <button id="copy_url" type="button" class="k-button k-button--default k-button--clipboard" data-clipboard-target="#public_url" title="copied">
                        <span class="k-icon-documents" aria-hidden="true"></span>
                        <span class="k-visually-hidden">Copy</span>
                    </button>
                </span>
            </div>
        </div>
    </div>

</div>
