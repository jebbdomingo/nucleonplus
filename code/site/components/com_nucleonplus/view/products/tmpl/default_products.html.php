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
    <? foreach ($products as $product): ?>
        <?
        if (!in_array($product->Type, ComQbsyncModelEntityItem::$item_types)) {
            continue;
        }
        ?>
        <div class="col-sm-6 col-md-4">
            <div class="thumbnail">
                <img src="<?= JURI::root() . 'images/' . $product->image ?>" alt="<?= $product->Name ?>" style="height: 304px" />
                <div class="caption">
                    <h3><?= $product->Name ?></h3>
                    <h4>&#8369;<?= number_format($product->UnitPrice, 2) ?></h4>
                    <p><?= $product->Description ?></p>
                    <!-- <table class="table">
                        <tr class="info">
                            <th>Price</th>
                            <th class="text-right">&#8369;<?= number_format($product->UnitPrice, 2) ?></th>
                        </tr>
                        <tr>
                            <td>Unilevel Direct Referral Bonus</td>
                            <td class="text-right">
                                <?
                                $dr_fee = ($product->drpv * $product->slots);
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
                                $ir_fee = ($product->irpv * $product->slots);
                                echo number_format($ir_fee, 2);
                                ?>
                            </td>
                        </tr>
                        <? if ($product->Type == 'Group'): ?>
                            <tr>
                                <td>Direct Referral Bonus</td>
                                <td class="text-right">
                                    <?
                                    $directReferral = $product->prpv * $product->slots;
                                    echo number_format($directReferral, 2);
                                    ?>
                                </td>
                            </tr>
                            <tr class="success">
                                <td>Commission <span class="label label-primary">New</span></td>
                                <td class="text-right">
                                    <?
                                    $patronages = ($product->prpv * $product->slots) * 2;
                                    echo number_format($patronages, 2);
                                    ?>
                                </td>
                            </tr>
                        <? endif ?>
                    </table> -->

                    <? if ($onlinePurchaseEnabled && $canBuy): ?>
                        <p>
                            <? if ($isAuthenticated): ?>
                                <form action="<?= route('view=cart') ?>" method="post">
                                    <input type="hidden" name="_action" value="add" />
                                    <input type="hidden" name="ItemRef" value="<?= $product->ItemRef ?>" />
                                    <input type="hidden" name="quantity" value="1" />
                                    <? if ($product->hasAvailableStock()): ?>
                                        <button class="btn btn-primary btn-md" role="button" <?= $disabled ?>>
                                            <span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>
                                                Add to cart
                                        </button>
                                    <? else: ?>
                                        <button class="btn btn-default btn-md" role="button" disabled="disabled">
                                            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                                                Out of stock
                                        </button>
                                    <? endif ?>
                                </form>
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