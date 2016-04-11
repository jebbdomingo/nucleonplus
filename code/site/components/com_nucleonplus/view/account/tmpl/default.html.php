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

<div class="row">

    <div class="col-md-4">

        <?= import('com://site/nucleonplus.account.account_summary.html', ['account' => $account]) ?>

    </div>

    <div class="col-md-8">

        <fieldset class="form-vertical">

            <form method="post" class="-koowa-grid">

                <?= import('com://site/nucleonplus.account.default_rewards.html', ['account' => $account]) ?>

                <?= import('com://admin/nucleonplus.account.default_direct_referrals.html', ['account' => $account]) ?>

            </form>

        </fieldset>

    </div>

</div>