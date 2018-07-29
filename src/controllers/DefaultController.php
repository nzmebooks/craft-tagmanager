<?php
/**
 * TagManager plugin for Craft CMS 3.x
 *
 * Plugin that allows you to edit and delete tags.
 *
 * @link      https://github.com/boboldehampsink
 * @copyright Copyright (c) 2018 Bob Olde Hampsink
 */

namespace boboldehampsink\tagmanager\controllers;

use boboldehampsink\tagmanager\TagManager;

use Craft;
use craft\web\Controller;

/**
 * @author    Bob Olde Hampsink
 * @package   TagManager
 * @since     2.0.0
 */
class DefaultController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['index', 'do-something'];

    // Public Methods
    // =========================================================================

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        $result = 'Welcome to the DefaultController actionIndex() method';

        return $result;
    }

    /**
     * @return mixed
     */
    public function actionDoSomething()
    {
        $result = 'Welcome to the DefaultController actionDoSomething() method';

        return $result;
    }
}
