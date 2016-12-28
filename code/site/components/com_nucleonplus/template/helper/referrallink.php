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
            'selector' => '.btn',
            'url'      => null,
        ));

        $url = $config->url . "&sponsor_id={$account->account_number}";

        $signature = md5(serialize(array($config->selector)));
        if (!isset(self::$_loaded[$signature])) {
            $html = "
            <script src=\"https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.5.13/clipboard.min.js\"></script>
            <script>
            kQuery(function($) {
                new Clipboard('{$config->selector}');

                $('{$config->selector}').tooltip({trigger: 'click'});
            });
            </script>
            ";

            $html .= '
            <div class="input-group">
                <span class="input-group-addon" id="basic-addon1">
                    <span class="glyphicon glyphicon-link" aria-hidden="true"></span> Referral Link
                </span>
                <input id="sponsor-link" type="text" class="form-control input-sm" value="' . $url . '" readonly="readonly" />
                <span class="input-group-btn">
                    <button class="btn btn-sm btn-primary" type="button" data-clipboard-target="#sponsor-link" title="Copied">
                        <span class="glyphicon glyphicon-copy" aria-hidden="true"></span> Copy
                    </button>
                </span>
            </div><!-- /input-group -->
            ';

            self::$_loaded[$signature] = true;
        }

        return $html;
    }
}
