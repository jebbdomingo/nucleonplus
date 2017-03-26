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

        if ($sponsor_id) {
            $this->getObject('user')->set('sponsor_id', $sponsor_id);
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
     * Overridden to only run if we have Nooku framework installed
     */
    public function update(&$args)
    {
        $return = null;

        if (class_exists('Koowa') && class_exists('KObjectManager') && (bool) JComponentHelper::getComponent('com_nucleonplus', true)->enabled)
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
