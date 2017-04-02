<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusTemplateHelperReferrallink extends ComKoowaTemplateHelperBehavior
{
    public function clipboard($config = array())
    {
        $user    = $this->getObject('user');
        $account = $this->getObject('com://admin/nucleonplus.model.accounts')
            ->user_id($user->getId())
            ->fetch()
        ;

        $config  = new KObjectConfigJson($config);
        $config->append(array(
            'selector' => '.k-button',
            'url'      => null,
            'item'     => null,
        ));

        $p   = strpos($config->url, '?') ? '&' : '?';
        $url = $config->url . $p . "sponsor_id={$account->account_number}";

        $signature = md5(serialize(array($config->selector, $config->item)));
        if (!isset(self::$_loaded[$signature])) {
            $html = "
            <script src=\"https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.5.13/clipboard.min.js\"></script>
            <script>
            kQuery(function($) {
                new Clipboard('{$config->selector}');
            });
            </script>
            ";

            $html .= '
            <div class="k-form-group">
                <div class="k-input-group k-input-group--small k-input-group--sponsor-link">
                    <label class="k-input-group__addon" for="sponsor_link">URL</label>
                    <input type="text" id="sponsor_link" class="k-form-control" value="' . $url . '" />
                    <span class="k-input-group__button">
                        <button id="copy_url" type="button" class="k-button k-button--default k-button--clipboard" data-clipboard-target="#sponsor_link" title="copied">
                            <span class="k-icon-documents" aria-hidden="true"></span>
                            <span class="k-visually-hidden">Copy</span>
                        </button>
                    </span>
                </div>
            </div>
            ';

            self::$_loaded[$signature] = true;
        }

        return $html;
    }
}
