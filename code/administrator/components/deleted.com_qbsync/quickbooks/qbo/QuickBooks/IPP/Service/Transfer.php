<?php

/**
 * 
 * 
 * Copyright (c) 2010 Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 * 
 * @license LICENSE.txt
 * @author Keith Palmer <Keith@ConsoliBYTE.com>
 * 
 * @package QuickBooks
 * @subpackage IPP
 */


QuickBooks_Loader::load('/QuickBooks/IPP/Service.php');

class QuickBooks_IPP_Service_Transfer extends QuickBooks_IPP_Service
{
    /**
     * Add a transfer to IDS/QuickBooks
     *
     * @param QuickBooks_IPP_Context $Context
     * @param string $realmID
     * @param QuickBooks_IPP_Object_Transfer $Object The Transfer to add
     * @return string                                The Id value of the new sales receipt
     */
    public function add($Context, $realmID, $Object)
    {
        return parent::_add($Context, $realmID, QuickBooks_IPP_IDS::RESOURCE_TRANSFER, $Object);
    }   
}