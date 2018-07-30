<?php
/**
 * TagManager plugin for Craft CMS 3.x
 *
 * Plugin that allows you to edit and delete tags.
 *
 * @link      https://github.com/boboldehampsink
 * @copyright Copyright (c) 2018 Bob Olde Hampsink
 */

namespace boboldehampsink\tagmanager\elements;

use boboldehampsink\tagmanager\TagManager;

use Craft;
use craft\elements\Tag;
use craft\elements\db\ElementQuery;
use craft\elements\db\ElementQueryInterface;

/**
 * @author    Bob Olde Hampsink
 * @package   TagManager
 * @since     2.0.0
 */
class TagManagerElementType extends Tag
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritDoc BaseElementModel::getCpEditUrl()
     *
     * @return string|false
     */
    public function getCpEditUrl()
    {
        $group = $this->getGroup();

        if ($group) {
            return Craft::$app->getUrlManager()::getCpUrl('tagmanager/' . $group->handle . '/' . $this->id);
        }
    }

    /**
     * @inheritdoc
     */
    public function getFieldLayout()
    {
        $tagGroup = $this->getGroup();

        if ($tagGroup) {
            return $tagGroup->getFieldLayout();
        }

        return null;
    }
}
