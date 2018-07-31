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
use craft\elements\db\TagQuery;
use craft\elements\db\ElementQuery;
use craft\elements\db\ElementQueryInterface;
use craft\helpers\UrlHelper;

/**
 * @author    Bob Olde Hampsink
 * @package   TagManager
 * @since     2.0.0
 */
class TagManagerElement extends Tag
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
            return UrlHelper::cpUrl('tagmanager/' . $group->handle . '/' . $this->id);
        }
    }

    protected static function defineTableAttributes() : array
    {
        return [
            'title' => \Craft::t('app', 'Title'),
        ];
    }
}
