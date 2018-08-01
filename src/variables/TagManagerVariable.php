<?php

/**
 * Tag Manager Variable.
 *
 * @author    Jason Darwin <b.oldehampsink@itmundi.nl>
 * @copyright Copyright (c) 2015, Jason Darwin
 * @license   http://buildwithcraft.com/license Craft License Agreement
 *
 * @link      http://github.com/nzmebooks
 */

namespace nzmebooks\tagmanager\variables;

use nzmebooks\tagmanager\TagManager;

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
