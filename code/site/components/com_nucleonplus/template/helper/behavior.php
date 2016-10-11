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
            'selector' => '.cartItemDeleteAction',
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
            'selector' => '.cartUpdateAction',
        ));

        $html = $this->koowa();

        $signature = md5(serialize(array($config->selector)));
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
     * Confirm cart
     *
     * @param array $config
     * 
     * @return string
     */
    public function confirmable($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'selector' => '.cartConfirmCheckoutAction',
        ));

        $html = $this->koowa();

        $signature = md5(serialize(array($config->selector)));
        if (!isset(self::$_loaded[$signature])) {
            $html .= "
            <script>
            kQuery(function($) {
                $('{$config->selector}').on('click', function(event){
                    event.preventDefault();

                    $('input[name=\"_action\"]').val('confirm');
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
     * Confirm checkout
     *
     * @param array $config
     * 
     * @return string
     */
    public function confirmCheckout($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'selector' => '.cartConfirmCheckoutAction',
        ));

        $html = $this->koowa();

        $signature = md5(serialize(array($config->selector,$config->confirm_message)));
        if (!isset(self::$_loaded[$signature])) {
            $html .= "
            <script>
            kQuery(function($) {
                $('{$config->selector}').on('click', function(event){
                    event.preventDefault();

                    $('input[name=\"_action\"]').val('confirmcheckout')
                    $('form[name=\"cartForm\"]').attr('action', '{$config->route}').submit();
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
            'selector' => '.cartCheckoutAction',
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

    /**
     * Cancel order
     *
     * @param array $config
     * 
     * @return string
     */
    public function orderCancellable($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'form'     => 'orderForm',
            'selector' => '.orderCancelAction',
            'action'   => 'cancelorder',
        ));

        $html = $this->koowa();

        $signature = md5(serialize(array($config->selector,$config->confirm_message)));
        if (!isset(self::$_loaded[$signature])) {
            $html .= "
            <script>
            kQuery(function($) {
                $('{$config->selector}').on('click', function(event){
                    event.preventDefault();

                    $('input[name=\"_action\"]').val('{$config->action}');
                    $('form[name=\"{$config->form}\"]').submit();
                });
            });
            </script>
            ";

            self::$_loaded[$signature] = true;
        }

        return $html;
    }
}
