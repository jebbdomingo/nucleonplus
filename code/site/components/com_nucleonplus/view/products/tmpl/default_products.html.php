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

<div class="k-gallery-container">
    <div class="k-gallery">
        <div class="k-gallery__items">
            <? foreach ($products as $product): ?>
                <?
                if (!in_array($product->Type, ComQbsyncModelEntityItem::$item_types)) {
                    continue;
                }
                ?>
                <div class="k-gallery__item k-gallery__item--file">

                    <div class="k-card">
                        <div class="k-card__body">
                            <div class="k-card__image">
                                <img src="<?= JURI::root() . 'images/' . $product->image ?>" alt="<?= $product->Name ?>" alt="card">
                            </div>
                            <div class="k-card__header">
                                <a href="<?= route("view=product&id={$product->id}") ?>"><?= $product->Name ?></a>
                            </div>
                            <div class="k-card__section">
                                &#8369; <?= number_format($product->UnitPrice, 2) ?>
                                <br />
                                <?= $product->Description ?>
                            </div>
                            <div class="k-card__footer">
                                <? if ($onlinePurchaseEnabled && $canBuy): ?>
                                    <form action="<?= route('view=cart') ?>" method="post">
                                        <div class="k-button-group">
                                            <a class="k-button k-button--primary" href="<?= route("view=product&id={$product->id}") ?>"><span class="k-icon-eye" aria-hidden="true"></span> View</a>

                                            <? if ($isAuthenticated): ?>
                                                    <input type="hidden" name="_action" value="add" />
                                                    <input type="hidden" name="ItemRef" value="<?= $product->ItemRef ?>" />
                                                    <input type="hidden" name="quantity" value="1" />
                                                    <? if ($product->hasAvailableStock()): ?>
                                                        <button class="k-button k-button--primary" role="button" <?= $disabled ?>>
                                                            <span class="k-icon-cart" aria-hidden="true"></span>
                                                                Add to cart
                                                        </button>
                                                    <? else: ?>
                                                        <button class="k-button k-button--default" role="button" disabled="disabled">
                                                            <span class="k-icon-warning" aria-hidden="true"></span>
                                                                Out of stock
                                                        </button>
                                                    <? endif ?>
                                            <? else: ?>
                                                <a href="<?= route('option=com_users&view=login') ?>" class="k-button k-button--default" role="button">
                                                    <span class="k-icon-account-login" aria-hidden="true"></span>
                                                    Buy Now
                                                </a>
                                            <? endif; ?>
                                        </div>
                                    </form>
                                <? endif ?>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="col-sm-9">
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
                    </div> -->

                </div>
            <? endforeach; ?>
        </div>
    </div>
</div>