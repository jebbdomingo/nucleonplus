<?
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

defined('KOOWA') or die;

$disabled = (!$isAuthenticated) ? 'disabled="disabled"' : null;
?>

<div class="row">
    <? foreach ($packages as $package): ?>
        <div class="col-sm-6 col-md-4">
            <div class="thumbnail">
                <div class="caption">
                    <h3><?= $package->name ?></h3>
                    <table class="table">
                        <tr class="info">
                            <th>Price</th>
                            <th class="text-right">P<?= number_format($package->price) ?></th>
                        </tr>
                        <tr>
                            <td>Unilevel Direct Referral Bonus</td>
                            <td class="text-right">
                                <?
                                $dr_fee = ($package->_rewardpackage_drpv * $package->_rewardpackage_slots);
                                echo number_format($dr_fee, 2);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Unilevel Indirect Referral Bonus<br />
                                <small>(Up to 20th Level)</small>
                            </td>
                            <td class="text-right">
                                <?
                                $ir_fee = ($package->_rewardpackage_irpv * $package->_rewardpackage_slots);
                                echo number_format($ir_fee, 2);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Direct Referral Bonus</td>
                            <td class="text-right">
                                <?
                                $directReferral = $package->_rewardpackage_prpv * $package->_rewardpackage_slots;
                                echo number_format($directReferral, 2);
                                ?>
                            </td>
                        </tr>
                        <? if ($package->_rewardpackage_type == 'package'): ?>
                            <tr class="success">
                                <td>Commission <span class="label label-primary">New</span></td>
                                <td class="text-right">
                                    <?
                                    $patronages = ($package->_rewardpackage_prpv * $package->_rewardpackage_slots) * 2;
                                    echo number_format($patronages, 2);
                                    ?>
                                </td>
                            </tr>
                        <? endif ?>
                        <? if ($package->_rewardpackage_type == 'package'): ?>
                            <tr>
                                <td colspan="2">
                                    <div class="well">
                                        <table class="table">
                                            <thead>
                                                <thead>
                                                    <th>Product</th>
                                                    <th class="text-right">Quantity</th>
                                                </thead>
                                            </thead>
                                            <tbody>
                                                <? foreach ($package->getItems() as $item): ?>
                                                    <tr>
                                                        <td><?= $item->_item_name ?></td>
                                                        <td class="text-right"><?= $item->quantity ?></td>
                                                    </tr>
                                                <? endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        <? endif ?>
                    </table>

                    <? if ($onlinePurchaseEnabled): ?>
                        <p>
                            <? if ($isAuthenticated): ?>
                                <a href="<?= route('view=order&package_id=' . $package->id . '&layout=form&tmpl=koowa') ?>" class="btn btn-primary btn-md" role="button" <?= $disabled ?>>
                                    <span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>
                                    Buy Now
                                </a>
                            <? else: ?>
                                <a href="<?= route('option=com_users&view=login') ?>" class="btn btn-default btn-md" role="button">
                                    <span class="glyphicon glyphicon-log-in" aria-hidden="true"></span>
                                    Buy Now
                                </a>
                            <? endif; ?>
                        </p>
                    <? endif; ?>
                </div>
            </div>
        </div>
    <? endforeach; ?>
</div>