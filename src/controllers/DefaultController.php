<?php

/**
 * Tag Manager Controller.
 *
 * Extends the default tag management options so we can edit and delete.
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@itmundi.nl>
 * @copyright Copyright (c) 2015, Bob Olde Hampsink
 * @license   http://buildwithcraft.com/license Craft License Agreement
 *
 * @link      http://github.com/boboldehampsink
 */

 namespace boboldehampsink\tagmanager\controllers;

use boboldehampsink\tagmanager\TagManager;
use boboldehampsink\tagmanager\models\TagModel;

use Craft;
use craft\web\Controller;
use craft\elements\Tag;
use craft\helpers\UrlHelper;

class DefaultController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    // protected $allowAnonymous = ['index', 'do-something'];

    // Public Methods
    // =========================================================================

    /**
     * Index.
     */
    public function actionIndex()
    {
        $this->renderTemplate('tagmanager/_index');
    }

    /**
     * Tag index.
     */
    public function actionTags()
    {
        $variables['groups'] = Craft::$app->getTags()->getAllTagGroups();
        $this->renderTemplate('tagmanager/_tags', $variables);
    }

    /**
     * Edit a tag using a supplied group handle.
     *
     * @param string $groupHandle
     */
    public function actionEditTagByGroupHandle($groupHandle)
    {
        $this->editTag(['groupHandle' => $groupHandle]);
    }

    /**
     * Edit a tag using a supplied group handle.
     *
     * @param string $groupHandle
     * @param string $groupId
     */
    public function actionEditTagByTagId($groupHandle, $tagId)
    {
        $this->editTag([
            'groupHandle' => $groupHandle,
            'tagId' => $tagId,
        ]);
    }

    /**
     * Edit a tag.
     *
     * @param array $variables
     *
     * @throws HttpException
     */
    public function editTag(array $variables = array())
    {
        if (!empty($variables['groupHandle'])) {
            $variables['group'] = Craft::$app->getTags()->getTagGroupByHandle($variables['groupHandle']);
        } elseif (!empty($variables['groupId'])) {
            $variables['group'] = Craft::$app->getTags()->getTagGroupById($variables['groupId']);
        }
        if (empty($variables['group'])) {
            throw new HttpException(404);
        }
        // Now let's set up the actual tag
        if (empty($variables['tag'])) {
            if (!empty($variables['tagId'])) {
                $siteId = Craft::$app->getSites()->getPrimarySite()->id;
                $variables['tag'] = Craft::$app->getTags()->getTagById((int) $variables['tagId'], $siteId);
                if (!$variables['tag']) {
                    throw new HttpException(404);
                }
            } else {
                $variables['tag'] = new TagModel();
                $variables['tag']->groupId = $variables['group']->id;
            }
        }
        // Tabs
        $variables['tabs'] = array();
        foreach ($variables['group']->getFieldLayout()->getTabs() as $index => $tab) {
            // Do any of the fields on this tab have errors?
            $hasErrors = false;
            if ($variables['tag']->hasErrors()) {
                foreach ($tab->getFields() as $field) {
                    if ($variables['tag']->getErrors($field->getField()->handle)) {
                        $hasErrors = true;
                        break;
                    }
                }
            }
            $variables['tabs'][] = array(
                'label' => $tab->name,
                'url' => '#tab' . ($index + 1),
                'class' => ($hasErrors ? 'error' : null),
            );
        }
        if (!$variables['tag']->id) {
            $variables['title'] = Craft::t('tagmanager', 'Create a new tag');
        } else {
            $variables['title'] = $variables['tag']->title;
        }
        // Breadcrumbs
        $variables['crumbs'] = array(
            array('label' => Craft::t('tagmanager', 'Tag Manager'), 'url' => UrlHelper::url('tagmanager')),
            array('label' => $variables['group']->name, 'url' => UrlHelper::url('tagmanager')),
        );
        // Set the "Continue Editing" URL
        $variables['continueEditingUrl'] = 'tagmanager/' . $variables['group']->handle . '/{id}';
        // Render the template!
        $this->renderTemplate('tagmanager/_edit', $variables);
    }

    /**
     * Saves a tag.
     */
    public function actionSaveTag()
    {
        $this->requirePostRequest();
        $request = Craft::$app->getRequest();

        $tagId = $request->getBodyParam('tagId');
        if ($tagId) {
            $siteId = Craft::$app->getSites()->getPrimarySite()->id;
            $tag = Craft::$app->getTags()->getTagById($tagId, $siteId);
            if (!$tag) {
                throw new Exception(Craft::t('tagmanager', 'No tag exists with the ID “{id}”', array('id' => $tagId)));
            }
        } else {
            $tag = new Tag();
        }
        // Set the tag attributes, defaulting to the existing values for whatever is missing from the post data
        $tag->groupId = $request->getBodyParam('groupId', $tag->groupId);
        $tag->title = $request->getBodyParam('title', $tag->title);
        // TODO: figure out what, if anything, we should do about the following line
        // $tag->setContentFromPost('fields');
        if (Craft::$app->getElements()->saveElement($tag)) {
            Craft::$app->getSession()->setNotice(Craft::t('tagmanager', 'Tag saved.'));
            $this->redirectToPostedUrl($tag);
        } else {
            Craft::$app->getSession()->setError(Craft::t('tagmanager', 'Couldn’t save tag.'));
            // Send the tag back to the template
            Craft::$app->getUrlManager()->setRouteParams([
                'tag' => $tag,
            ]);
        }

        if ($request->getBodyParam('redirect')) {
            return $this->redirect($request->getBodyParam('redirect'));
        } else {
            return $this->redirectToPostedUrl();
        }
    }

    /**
     * Deletes a tag.
     */
    public function actionDeleteTag()
    {
        $this->requirePostRequest();
        $request = Craft::$app->getRequest();

        $tagId = $request->getBodyParam('tagId');
        if (Craft::$app->getElements()->deleteElementById($tagId)) {
            Craft::$app->getSession()->setNotice(Craft::t('tagmanager', 'Tag deleted.'));

            if ($request->getBodyParam('redirect')) {
                return $this->redirect($request->getBodyParam('redirect'));
            } else {
                return $this->redirectToPostedUrl();
            }
        } else {
            Craft::$app->getSession()->setError(Craft::t('tagmanager', 'Couldn’t delete tag.'));
        }
    }
}
