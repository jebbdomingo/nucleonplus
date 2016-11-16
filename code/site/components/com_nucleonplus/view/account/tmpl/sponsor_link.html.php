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
        <h3 class="page-header"><span class="glyphicon glyphicon-link" aria-hidden="true"></span> <?= translate('Referral Link') ?></h3>
        <p class="text-info">Copy this link to refer a friend and start earning</p>
        <textarea class="sponsor_link" readonly="readonly" style="width: 100%; height: 75px"><?= JURI::root() . "index.php/component/users/?view=registration&sponsor_id={$account->account_number}" ?></textarea>
    </div>