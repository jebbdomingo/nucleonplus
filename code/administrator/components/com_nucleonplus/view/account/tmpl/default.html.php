<?
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

defined('KOOWA') or die; ?>

<?= helper('behavior.koowa'); ?>

<ktml:style src="media://koowa/com_koowa/css/koowa.css" />
<ktml:style src="media://com_nucleonplus/css/admin-read.css" />
<ktml:style src="media://com_nucleonplus/css/admin-account-read.css" />

<ktml:module position="toolbar">
    <ktml:toolbar type="actionbar" title="<?= object('user.provider')->load($account->user_id)->getName(); ?>" icon="task-add icon-book">
</ktml:module>

<div class="row-fluid">

    <div class="span3">

        <fieldset class="form-vertical">

            <?= import('com://admin/nucleonplus.account.default_account_summary.html', ['account' => $account]) ?>

        </fieldset>

    </div>

    <div class="span9">

        <fieldset class="form-vertical">

            <form method="post" class="-koowa-grid">
                
                <?= import('com://admin/nucleonplus.account.default_rewards.html', ['account' => $account]) ?>

                <?= import('com://admin/nucleonplus.account.default_orders.html', ['account' => $account]) ?>

                <?= import('com://admin/nucleonplus.account.default_other_info.html', ['account' => $account]) ?>

                <?= import('com://admin/nucleonplus.account.default_direct_referrals.html', ['account' => $account]) ?>

            </form>

        </fieldset>

    </div>

</div>