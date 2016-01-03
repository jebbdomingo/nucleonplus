<?
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */
?>

<? foreach ($accounts as $account): ?>
    <tr>
        <td style="text-align: center;">
            <?= helper('grid.checkbox', array('entity' => $account)) ?>
        </td>
        <td class="deskman_table__title_field">
            <a href="<?= route('view=account&id='.$account->id); ?>">
                <?= object('user.provider')->load($account->user_id)->getName() ?>
            </a>
        </td>
        <td><?= $account->getAuthor()->getName() ?></td>
        <td>
            <span class="label <?= ($account->status == 'closed') ? 'label-default' : 'label-info' ?>"><?= ucwords(escape($account->status)) ?></span>
        </td>
    </tr>
<? endforeach; ?>