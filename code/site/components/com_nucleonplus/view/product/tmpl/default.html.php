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

<?= helper('bootstrap.load', array('javascript' => true)); ?>
<?= helper('behavior.koowa'); ?>

<div class="row">

    <div class="col-sm-4">
        <img src="<?= JURI::root() . 'images/' . $product->image ?>" alt="<?= $product->Name ?>" style="width: 300px" />
    </div>

    <div class="col-sm-8">

        <div class="caption">
            <h3><?= $product->Name ?></h3>
            <p><?= $product->Description ?></p>

            <? if ($onlinePurchaseEnabled && $canBuy && $isAuthenticated): ?>
                <div class="row">

                    <div class="col-sm-8">

                        <?= helper('referrallink.clipboard', array('url' => JUri::root() . "products.html?view=product&id={$product->id}")) ?>
                        <span id="helpBlock" class="help-block small">Share this product link to refer a friend and start earning</span>

                    </div>

                </div>
            <? endif ?>

            <hr />

            <h4>&#8369; <?= number_format($product->UnitPrice, 2) ?></h4>

            <hr />

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

<br />

<div class="row">

    <div class="col-sm-12">

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#description" aria-controls="description" role="tab" data-toggle="tab">Description</a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="description">...</div>
        </div>

    </div>

</div>