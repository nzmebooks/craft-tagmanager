<?php

/**
 * TagManager plugin for Craft CMS 3.x
 *
 * Plugin that allows you to edit and delete tags.
 *
 * @link      https://github.com/nzmebooks
 * @copyright Copyright (c) 2018 Jason Darwin
 */
namespace nzmebooks\tagmanager\models;

use nzmebooks\tagmanager\TagManager;

use Craft;
use craft\base\Model;

class TagModel extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $groupId;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'groupId'], 'number'],
        ];
    }
}
