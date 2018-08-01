<?php

/**
 * Tag Manager Variable.
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@itmundi.nl>
 * @copyright Copyright (c) 2015, Bob Olde Hampsink
 * @license   http://buildwithcraft.com/license Craft License Agreement
 *
 * @link      http://github.com/boboldehampsink
 */

namespace boboldehampsink\tagmanager\variables;

use boboldehampsink\tagmanager\TagManager;

use Craft;

class TagManagerVariable
{
    /**
     * @method getTags
     * @return array
     */
    public function getTags()
    {
        return TagManager::$plugin->tagManagerService->getTags();
    }

    /**
     * @method getTagCount
     * @return array
     */
    public function getTagCountsForEntry($entryId)
    {
        return TagManager::$plugin->tagManagerService->getTagCountsForEntry($entryId);
    }

    public function getPluginName()
    {
        return TagManager::$plugin->getPluginName();
    }
}
