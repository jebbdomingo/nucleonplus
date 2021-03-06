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

<div class="well bg-info">
    <h4 class="page-header"><span class="glyphicon glyphicon-link" aria-hidden="true"></span> <?= translate('Referral Link') ?></h4>

    <?= helper('referrallink.clipboard', array('url' => JURI::root() . "sign-up")) ?>

    <span id="helpBlock" class="help-block small">Share this link to refer a friend and start earning</span>
</div>