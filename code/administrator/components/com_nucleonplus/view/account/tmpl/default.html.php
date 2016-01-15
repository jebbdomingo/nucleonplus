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

<?= helper('behavior.koowa'); ?>

<ktml:style src="media://koowa/com_koowa/css/koowa.css" />
<ktml:style src="media://com_nucleonplus/css/admin-read.css" />

<ktml:module position="toolbar">
    <ktml:toolbar type="actionbar" title="<?= object('user.provider')->load($account->user_id)->getName(); ?>" icon="task-add icon-book">
</ktml:module>

<div class="row-fluid">
    <div class="span9">
        <fieldset class="form-vertical">
            <form method="post" class="-koowa-grid">
                
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            Orders
                            <a class="btn btn-default" href="<?= route('layout=order-form&id='.$account->id) ?>" role="button">Buy Product Package</a>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped">
                            <thead>
                                <th>Order No.</th>
                                <th>Product Package</th>
                                <th>Slots</th>
                                <th>Price</th>
                                <th>Order Status</th>
                                <th>Invoice Status</th>
                                <th>Ordered On</th>
                            </thead>
                            <tbody>
                                <? if (count($purchases = $account->getPurchases()) > 0): ?>
                                    <? foreach ($purchases as $order): ?>
                                        <tr>
                                            <td>
                                                <a href="<?= route('view=order&id='.$order->id) ?>"><?= $order->id ?></a>
                                            </td>
                                            <td><?= $order->package_name ?></td>
                                            <td><?= $order->package_slots ?></td>
                                            <td><?= $order->package_price ?></td>
                                            <td><span class="label label-<?= ($order->order_status == 'cancelled') ? 'default' : 'info' ?>"><?= ucwords(escape($order->order_status)) ?></span></td>
                                            <td><span class="label label-<?= ($order->invoice_status == 'sent') ? 'default' : 'info' ?>"><?= ucwords(escape($order->invoice_status)) ?></span></td>
                                            <td>
                                                <div><?= helper('date.humanize', array('date' => $order->created_on)) ?></div>
                                                <div><?= $order->created_on ?></div> 
                                            </td>
                                        </tr>
                                    <? endforeach ?>
                                <? else: ?>
                                    <tr>
                                        <td colspan="7">
                                            <p class="text-center">No Purchase(s) Yet</p>
                                        </td>
                                    </tr>
                                <? endif ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Direct Referrals</h3>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped">
                            <thead>
                                <th>Name</th>
                                <th>Account No.</th>
                            </thead>
                            <tbody>
                                <? if (count($account->getDirectReferrals()) > 0): ?>
                                    <? foreach ($account->getDirectReferrals() as $referral): ?>
                                        <tr>
                                            <td><?= object('user.provider')->load($referral->user_id)->getName() ?></td>
                                            <td><?= $referral->account_number ?></td>
                                        </tr>
                                    <? endforeach ?>
                                <? else: ?>
                                    <tr>
                                        <td colspan="2">
                                            <p class="text-center">No Direct Referrals</p>
                                        </td>
                                    </tr>
                                <? endif ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            Other Info.
                        </h3>
                    </div>
                    <div class="panel-body">
                        <?= $account->note ?>
                    </div>
                </div>

            </form>
        </fieldset>
    </div>

    <div class="span3">
        <fieldset class="form-vertical">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= translate('Account Summary'); ?></h3>
                </div>
                <table class="table">
                    <tbody>
                        <tr>
                            <td><label><strong><?= translate('Account No.') ?></strong></label></td>
                            <td><?= $account->account_number ?></td>
                        </tr>
                        <tr>
                            <td><label><strong><?= translate('Account Status'); ?></strong></label></td>
                            <td><span class="label label-<?= ($account->status == 'closed') ? 'default' : 'info' ?>"><?= ucwords(escape($account->status)) ?></span></td>
                        </tr>
                        <tr>
                            <td><label><strong><?= translate('Account Created On') ?></strong></label></td>
                            <td>
                                <div><?= helper('date.humanize', array('date' => $account->created_on)) ?></div>
                                <div><?= $account->created_on ?></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </fieldset>
    </div>
</div>