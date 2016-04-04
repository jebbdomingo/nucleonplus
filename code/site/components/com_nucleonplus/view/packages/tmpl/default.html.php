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

<div class="row">

    <div class="col-md-12">

        <fieldset class="form-vertical">

            <form method="post" class="-koowa-grid">

                <?= import('com://site/nucleonplus.packages.default_packages.html', ['packages' => $packages]) ?>

            </form>

        </fieldset>

    </div>

</div>