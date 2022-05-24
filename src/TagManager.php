<?php
/**
 * TagManager plugin for Craft CMS 3.x
 *
 * Plugin that allows you to edit and delete tags.
 *
 * @link      https://github.com/nzmebooks
 * @copyright Copyright (c) 2018 Jason Darwin
 */

namespace nzmebooks\tagmanager;

use nzmebooks\tagmanager\elements\TagManagerElement;
use nzmebooks\tagmanager\services\TagManagerService;
use nzmebooks\tagmanager\variables\TagManagerVariable;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\services\Elements;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\web\twig\variables\CraftVariable;

use yii\base\Event;

/**
 * Class TagManager
 *
 * @author    Jason Darwin
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
    public string $schemaVersion = '2.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->setComponents([
            'tagManagerService' => TagManagerService::class,
        ]);

        // register the actions
        // Event::on(
        //     UrlManager::class,
        //     UrlManager::EVENT_REGISTER_SITE_URL_RULES,
        //     function (RegisterUrlRulesEvent $event) {
        //         $event->rules['tagmanager/save-tag'] = 'tagmanager/default/save-tag';
        //     }
        // );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['tagmanager'] = 'tagmanager/default/index';
                $event->rules['tagmanager/tags'] = 'tagmanager/default/tags';
                $event->rules['tagmanager/<groupHandle:[^\/]+>/<tagId:\d+>'] = 'tagmanager/default/edit-tag-by-tag-id';
                $event->rules['tagmanager/<groupHandle:[^\/]+>/new'] = 'tagmanager/default/edit-tag-by-group-handle';
                $event->rules['tagmanager/<groupHandle:[^\/]+>/<tagId:\d+>/new'] = 'tagmanager/default/edit-tag-by-tag-id';
            }
        );

        // register our variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('tagManager', TagManagerVariable::class);
            }
        );

        // Event::on(
        //     Elements::class,
        //     Elements::EVENT_REGISTER_ELEMENT_TYPES,
        //     function (RegisterComponentTypesEvent $event) {
        //         $event->types[] = TagManagerElementType::class;
        //     }
        // );

        Craft::info(
            Craft::t(
                'tagmanager',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    /**
     * Returns the user-facing name of the plugin, which can override the name
     * in composer.json
     *
     * @return string
     */
    public function getPluginName()
    {
        return Craft::t('tagmanager', 'Tag Manager');
    }
}
