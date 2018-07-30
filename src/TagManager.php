<?php
/**
 * TagManager plugin for Craft CMS 3.x
 *
 * Plugin that allows you to edit and delete tags.
 *
 * @link      https://github.com/boboldehampsink
 * @copyright Copyright (c) 2018 Bob Olde Hampsink
 */

namespace boboldehampsink\tagmanager;


use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\services\Elements;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

/**
 * Class TagManager
 *
 * @author    Bob Olde Hampsink
 * @package   TagManager
 * @since     2.0.0
 *
 */
class TagManager extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var TagManager
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '2.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['tagmanager'] = 'tagmanager/default/tag-index';
                $event->rules['tagmanager/<groupHandle:[^\/]+>/new'] = 'tagmanager/default/edit-tag-by-group-handle';
                $event->rules['tagmanager/<groupHandle:[^\/]+>/<tagId:[^\/]+>/new'] = 'tagmanager/default/edit-tag-by-tag-id';
            }
        );

        Event::on(
            Elements::class,
            Elements::EVENT_REGISTER_ELEMENT_TYPES,
            function (RegisterComponentTypesEvent $event) {
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                }
            }
        );

        Craft::info(
            Craft::t(
                'tagmanager',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

}
