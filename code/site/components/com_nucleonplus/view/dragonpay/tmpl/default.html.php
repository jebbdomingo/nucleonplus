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
<?= helper('bootstrap.load', array('javascript' => true)); ?>
<?= helper('behavior.keepalive'); ?>
<?= helper('behavior.validator'); ?>

<?
$context = 'default';
$icon    = null;
$title   = null;
$message = object('request')->query->message;
$status  = object('request')->query->status;

if ($status == 'S')
{
    $context = 'success';
    $icon    = 'glyphicon-ok';
    $title   = 'Payment Successful';
}
elseif ($status == 'P')
{
    $context = 'warning';
    $icon    = 'glyphicon-hourglass';
    $title   = 'Pending Payment';
}
elseif ($status == 'F')
{
    $context = 'danger';
    $icon    = 'glyphicon-warning-sign';
    $title   = 'Payment Transaction Failed';
}
?>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-<?= $context ?>">
            <div class="panel-heading">
                <div class="panel-title">
                    <h5><span class="glyphicon <?= $icon ?>"></span> <?= $title ?></h5>
                </div>
            </div>
            <div class="panel-body">
                <?= $message ?>
            </div>
        </div>
    </div>
</div>
