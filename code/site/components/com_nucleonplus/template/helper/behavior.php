<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusTemplateHelperBehavior extends ComKoowaTemplateHelperBehavior
{
    /**
     * Makes delete button actions
     *
     * @param array $config
     * 
     * @return string
     */
    public function deletable($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'selector' => '.deleteAction',
        ));

        $html = $this->koowa();

        $signature = md5(serialize(array($config->selector,$config->confirm_message)));
        if (!isset(self::$_loaded[$signature])) {
            $html .= "
            <script>
            kQuery(function($) {
                $('{$config->selector}').on('click', function(event){
                    event.preventDefault();
                    
                    var id = $(this).data('id');

                    $('input[name=\"_action\"]').val('deleteitem');
                    $('input[name=\"item_id\"]').val(id);
                    $('form[name=\"cartForm\"]').submit();
                });
            });
            </script>
            ";

            self::$_loaded[$signature] = true;
        }

        return $html;
    }

    /**
     * Update cart
     *
     * @param array $config
     * 
     * @return string
     */
    public function updatable($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'selector' => '.updateCartAction',
        ));

        $html = $this->koowa();

        $signature = md5(serialize(array($config->selector,$config->confirm_message)));
        if (!isset(self::$_loaded[$signature])) {
            $html .= "
            <script>
            kQuery(function($) {
                $('{$config->selector}').on('click', function(event){
                    event.preventDefault();

                    $('input[name=\"_action\"]').val('updatecart');
                    $('form[name=\"cartForm\"]').submit();
                });
            });
            </script>
            ";

            self::$_loaded[$signature] = true;
        }

        return $html;
    }

    /**
     * Checkout
     *
     * @param array $config
     * 
     * @return string
     */
    public function checkout($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'selector' => '.checkoutAction',
        ));

        $html = $this->koowa();

        $signature = md5(serialize(array($config->selector,$config->confirm_message)));
        if (!isset(self::$_loaded[$signature])) {
            $html .= "
            <script>
            kQuery(function($) {
                $('{$config->selector}').on('click', function(event){
                    event.preventDefault();

                    $('input[name=\"_action\"]').val('add')
                    $('form[name=\"cartForm\"]').attr('action', '{$config->route}').submit();
                });
            });
            </script>
            ";

            self::$_loaded[$signature] = true;
        }

        return $html;
    }
}
