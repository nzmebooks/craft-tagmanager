<?php
/**
 * TagManager plugin for Craft CMS 3.x
 *
 * Plugin that allows you to edit and delete tags.
 *
 * @link      https://github.com/boboldehampsink
 * @copyright Copyright (c) 2018 Bob Olde Hampsink
 */

namespace boboldehampsink\tagmanager\models;

use boboldehampsink\tagmanager\TagManager;

use Craft;
use craft\base\Model;

/**
 * @author    Bob Olde Hampsink
 * @package   TagManager
 * @since     2.0.0
 */
class TagManagerModel extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $someAttribute = 'Some Default';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['someAttribute', 'string'],
            ['someAttribute', 'default', 'value' => 'Some Default'],
        ];
    }
}
