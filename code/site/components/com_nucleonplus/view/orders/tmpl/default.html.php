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

<?= helper('bootstrap.load', array('javascript' => true)); ?>
<?= helper('behavior.koowa'); ?>

<ktml:style src="media://koowa/com_koowa/css/admin.css" />
<ktml:style src="media://com_nucleonplus/css/site-styles.css" />

<div class="row">

    <div class="col-md-12">

        <p><a href="<?= route('view=order&layout=form&tmpl=koowa') ?>" class="btn btn-primary">Buy Product Package</a></p>

        <fieldset class="form-vertical">

            <form method="post" class="-koowa-grid">

                <?= import('com://site/nucleonplus.orders.default_orders.html', ['orders' => $orders]) ?>

            </form>

        </fieldset>

    </div>

</div>