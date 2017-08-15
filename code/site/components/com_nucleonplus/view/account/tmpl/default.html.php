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

<?= helper('ui.load', array(
    'domain' => 'admin'
)); ?>

<?= helper('behavior.clipboard') ?>

<? // Add template class to visually enclose the forms ?>
<script>document.documentElement.className += " k-frontend-ui";</script>

<!-- Wrapper -->
<div class="k-wrapper k-js-wrapper">

    <!-- Overview -->
    <div class="k-content-wrapper">

        <!-- Sidebar -->
        <?= import('default_sidebar.html'); ?>

        <!-- Content -->
        <div class="k-content k-js-content">

            <!-- Title when sidebar is invisible -->
            <ktml:toolbar type="titlebar" title="Nucleon Plus" mobile>

            <!-- Component -->
            <div class="k-component-wrapper">
                <div class="k-container">
                    <!-- Member's manual alert -->
                    <div class="k-alert k-alert--info">
                        <span class="k-icon-cloud-download" aria-hidden="true"></span> Download <a href="media://com_nucleonplus/members-manual.pdf" target="_blank"><?= translate('Member\'s Manual') ?></a>
                        <span id="helpBlock" class="help-block small"> - All you need to know on how to earn in Nucleon +</span>
                    </div>

                    <div class="k-well">
                        <p>Sponsor Link</p>
                        <div class="k-form-group">
                            <div class="k-input-group k-input-group--small k-input-group--public-url">
                                <label class="k-input-group__addon" for="public_url">URL</label>
                                <?
                                $p   = strpos(JURI::root(), '?') ? '&' : '?';
                                $url = JURI::root() . $p . "sponsor_id={$account->id}";
                                ?>
                                <input type="text" id="public_url" class="k-form-control" value="<?= $url ?>" />
                                <span class="k-input-group__button">
                                    <button id="copy_url" type="button" class="k-button k-button--default k-button--clipboard" data-clipboard-target="#public_url" title="copied">
                                        <span class="k-icon-documents" aria-hidden="true"></span>
                                        <span class="k-visually-hidden">Copy</span>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>

                    <fieldset class="k-form-block">
                        <div class="k-form-block__header">
                            <?= translate('Account Details') ?>
                            <a href="<?= route('view=member&layout=form&tmpl=koowa') ?>" class="k-button k-button--info k-button--small" data-clipboard-text="<?= $url ?>">Edit</a>
                        </div>
                        <div class="k-form-block__content">
                            <div class="k-table-container">
                                <div class="k-table">
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td>Name</td>
                                                <td><?= $account->_name ?></td>
                                            </tr>
                                            <tr>
                                                <td>Account Number</td>
                                                <td><?= $account->id ?></td>
                                            </tr>
                                            <tr>
                                                <td>Sponsor ID</td>
                                                <td><?= $account->sponsor_id ?></td>
                                            </tr>
                                            <tr>
                                                <td>Member Since</td>
                                                <td><?= helper('date.humanize', array('date' => $account->created_on)) ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>

        </div>

    </div>

</div>