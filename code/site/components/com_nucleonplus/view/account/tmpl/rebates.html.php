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

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?= translate('My Rebates') ?></h3>
    </div>
    <div class="panel-body">
        <table class="table">
            <thead>
                <th>Order #</th>
                <th>Product Package</th>
                <th>Slots</th>
                <th class="text-right">Points</th>
            </thead>
            <tbody>
                <? foreach ($rewards as $reward): ?>
                    <tr>
                        <td>
                            <input type="hidden" name="reward[]" value="<?= $reward->id ?>" />
                            <?= $reward->product_id ?>
                        </td>
                        <td><?= $reward->product_name ?></td>
                        <td><?= $reward->slots ?></td>
                        <th class="text-right"><?= number_format($reward->total, 2) ?></th>
                    </tr>
                <? endforeach ?>
            </tbody>
        </table>

        <p class="pull-right"><a class="btn btn-primary btn-md" href="#" role="button">Encash</a></p>
    </div>
</div>