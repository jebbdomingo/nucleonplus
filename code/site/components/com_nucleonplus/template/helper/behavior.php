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
                    $('form[name=\"k-js-cart-form\"]').submit();
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
                    $('form[name=\"k-js-cart-form\"]').submit();
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
                    $('form[name=\"k-js-cart-form\"]').submit();
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
                    $('form[name=\"k-js-cart-form\"]').attr('action', '{$config->route}').submit();
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

                    if (confirm('Cancelled order cannot be recovered, would you like to continue?'))
                    {
                        var form  = $(this).closest('form');
                        var input = '<input type=\"hidden\" name=\"_action\" value=\"{$config->action}\"  />';

                        form.append(input).submit();
                    }
                });
            });
            </script>
            ";

            self::$_loaded[$signature] = true;
        }

        return $html;
    }

    /**
     * Mark order as delivered
     *
     * @param array $config
     * 
     * @return string
     */
    public function orderDeliverable($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'selector' => '.orderDeliverable',
            'action'   => 'markdelivered',
        ));

        $html = $this->koowa();

        $signature = md5(serialize(array($config->selector,$config->confirm_message)));
        if (!isset(self::$_loaded[$signature])) {
            $html .= "
            <script>
            kQuery(function($) {
                $('{$config->selector}').on('click', function(event){
                    event.preventDefault();

                    var form  = $(this).closest('form');
                    var input = '<input type=\"hidden\" name=\"_action\" value=\"{$config->action}\"  />';

                    form.append(input);
                    form.submit();
                });
            });
            </script>
            ";

            self::$_loaded[$signature] = true;
        }

        return $html;
    }

    public function clipboard($config = array())
    {
        $config  = new KObjectConfigJson($config);
        $config->append(array(
            'selector'        => '.k-button--clipboard',
            'success_message' => 'Copied to the clipboard',
        ));

        $signature = md5(serialize(array($config->selector)));

        if (!isset(self::$_loaded[$signature])) {
            $html = "
            <script src=\"media://com_nucleonplus/js/clipboard.min.js\"></script>
            <script>
            kQuery(function($) {
                var clipboard = new Clipboard('{$config->selector}');

                clipboard.on('success', function(e) {
                    console.log(e);
                    alert('{$config->success_message}');
                });
                clipboard.on('error', function(e) {
                    console.log(e);
                });
            });
            </script>
            ";

            self::$_loaded[$signature] = true;
        }

        return $html;
    }
}
