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

    <div class="input-group">
        <input id="sponsor-link" type="text" class="form-control input-sm" value="<?= JURI::root() . "sign-up/?sponsor_id={$account->account_number}" ?>" readonly="readonly" />
        <span class="input-group-btn">
            <button class="btn btn-sm btn-default" type="button" data-clipboard-target="#sponsor-link" title="Copied">
                <span class="glyphicon glyphicon-copy" aria-hidden="true"></span> Copy
            </button>
        </span>
    </div><!-- /input-group -->

    <span id="helpBlock" class="help-block small">Copy this link to refer a friend and start earning</span>
</div>