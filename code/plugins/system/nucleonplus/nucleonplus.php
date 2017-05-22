<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class PlgSystemNucleonplus extends JPlugin
{
    /**
     * Hook into onAfterRoute sytem event
     *
     * @return void
     */
    public function onAfterRoute()
    {
        // Render the sticky toolbar
        $this->_renderToolbar();
    }

    public function onAfterDispatch()
    {
        $request = $this->getObject('request');
        $input   = JFactory::getApplication()->input;

        // Remember sponsor id from referral link
        $this->_persistSponsorId();

        // Social meta tags for product page
        $view = $input->get('view', null);
        $id   = $input->get('id', null);

        if ($view == 'product' && $id) {
            $this->_generateSocialMetatags($id);
        }
    }

    /**
     * Persist sponsor id
     * 
     * @return void
     */
    protected function _persistSponsorId()
    {
        $input      = JFactory::getApplication()->input;
        $sponsor_id = $input->get('sponsor_id', null);

        if ($sponsor_id)
        {
            $session = $this->getObject('lib:user.session');
            $session->set('sponsor_id', $sponsor_id);
        }
    }

    /**
     * Generate social meta tags from item data
     * 
     * @param  integer $product_id
     * 
     * @return void
     */
    protected function _generateSocialMetatags($product_id)
    {
        $item = $this->getObject('com://admin/qbsync.model.items')->id($product_id)->fetch();

        $image = JURI::base() . "images/{$item->image}";

        $document = JFactory::getDocument();
        $document->setMetaData('twitter:card', 'summary');
        $document->setMetaData('twitter:title', $item->Name);
        $document->setMetaData('twitter:description', $item->Description);
        $document->setMetaData('twitter:image:alt', $item->Name);
        $document->setMetaData('twitter:image', $image);
        $document->setMetaData('og:type', 'item');
        $document->setMetaData('og:title', $item->Name);
        $document->setMetaData('og:description', $item->Description);
        $document->setMetaData('og:url', JURI::base());
        $document->setMetaData('og:image', $image);
    }

    /**
     * Renders the sticky toolbar that provides access to the dashboard.
     *
     * @return void
     */
    protected function _renderToolbar()
    {
        $request = $this->getObject('request')->getQuery();
        $input   = JFactory::getApplication()->input;
        $option  = $input->get('option', 'cmd');
        $view    = $input->get('view', 'cmd');
        $layout  = $input->get('layout', null);
        $id      = $input->get('id', null);

        // Permissions
        $user        = $this->getObject('user');
        $isAuthentic = $user->isAuthentic();

        // Only show the edit bar with the specified specifications above
        $baseUrl = JURI::root();
        $token   = JSession::getFormToken();
        $return  = urlencode(base64_encode($baseUrl));

        if (in_array(6, $user->getGroups())) {
            $dashboard_url = $baseUrl . 'index.php?option=com_adminplus&view=accounts';
        } else {
            $dashboard_url = $baseUrl . 'index.php?option=com_nucleonplus&view=account';
        }

        $config = new KObjectConfigJson();
        $config->append(array(
            'options' => array(
                'id'          => $id,
                'isAuthentic' => $isAuthentic,
                'url'         => array(
                    'homeUrl'      => JRoute::_($baseUrl),
                    'dashboardUrl' => JRoute::_($dashboard_url),
                    'loginUrl'     => JRoute::_('index.php?option=com_users&view=login'),
                    'logoutUrl'    => JRoute::_("index.php?option=com_users&task=user.logout&{$token}=1&return={$return}")
                ),
            )
        ));

        $doc = JFactory::getDocument();
        $doc->addScriptDeclaration('Nucleonplus.ToolBar.init('.$config->options.');');
        $doc->addStyleSheet(JURI::base() . 'media/com_nucleonplus/css/toolbar.css');
        $doc->addScript(JURI::base() . 'media/com_nucleonplus/js/nucleonplus.toolbar.js');
        $doc->addScript(JURI::base() . 'media/com_nucleonplus/js/toolbar.js');
    }

    /**
     * Get an instance of an object identifier
     *
     * @param KObjectIdentifier|string $identifier An ObjectIdentifier or valid identifier string
     * @param array                    $config     An optional associative array of configuration settings.
     * @return KObjectInterface  Return object on success, throws exception on failure.
     */
    final public function getObject($identifier, array $config = array())
    {
        return KObjectManager::getInstance()->getObject($identifier, $config);
    }

    /**
     * Only run this when:
     * - Request method is GET
     * - Document type is HTML
     * - We are on site app
     * 
     * @return bool
     */
    protected function _canRun()
    {
        return (JFactory::getApplication()->input->getMethod() === 'GET'
            && JFactory::getDocument()->getType() === 'html'
            && JFactory::getApplication()->isSite());
    }

    /**
     * Overridden to only run if we have Nooku framework installed
     */
    public function update(&$args)
    {
        $return = null;

        if (class_exists('Koowa') && class_exists('KObjectManager') && (bool) JComponentHelper::getComponent('com_nucleonplus', true)->enabled && $this->_canRun())
        {
            try
            {
                $return = parent::update($args);
            }
            catch (Exception $e)
            {
                if (JDEBUG) {
                    JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
                }
            }
        }

        return $return;
    }
}
