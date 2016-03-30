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

<?= helper('bootstrap.load', array('javascript' => true)); ?>
<?= helper('behavior.koowa'); ?>
<?= helper('behavior.modal'); ?>
<?= helper('behavior.keepalive'); ?>
<?= helper('behavior.validator'); ?>

<ktml:style src="media://koowa/com_koowa/css/koowa.css" />

<? // Toolbar ?>
<?= import('com://site/nucleonplus.account.rewards_manage.html') ?>

<div class="koowa_form">

    <form method="post" class="form-horizontal -koowa-form" action="<?= route('option=com_nucleonplus&view=payout'); ?>">

        <div class="row-fluid">

            <div class="span12">

                <?= import('com://site/nucleonplus.account.rebates.html', ['rebates' => $rebates]) ?>

                <?= import('com://site/nucleonplus.account.referral_bonuses.html') ?>

            </div>

        </div>
        
    </form>

</div>