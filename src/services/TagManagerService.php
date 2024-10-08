<?php

/**
 * Tag Manager Service.
 *
 * @author    Jason Darwin <b.oldehampsink@itmundi.nl>
 * @copyright Copyright (c) 2015, Jason Darwin
 * @license   http://buildwithcraft.com/license Craft License Agreement
 *
 * @link      http://github.com/nzmebooks
 */

namespace nzmebooks\tagmanager\services;

use nzmebooks\tagmanager\TagManager;

use Craft;
use craft\base\Component;
use craft\db\Query;

class TagManagerService extends Component
{
    public function cpTrigger()
    {
        return Craft::$app->config->general->cpTrigger;
    }

    public function getTags()
    {
        $query = (new Query())
            ->select('
                tags.id                     AS id,
                contentEntries.elementId    AS elementId,
                taggroups.name              AS groupName,
                taggroups.handle            AS groupHandle,
                elements_sites.title        AS title,
                elements_sites.dateCreated  AS dateCreated,
                elements_sites.dateUpdated  AS dateUpdated,
                entries.id                  AS entryId,
                contentEntries.title        AS entryTitle,
                sectionsEntries.handle      AS entrySection
            ')
            ->from('elements_sites')
            ->join('JOIN', 'tags           AS tags',            'elements_sites.elementId = tags.id')
            ->join('JOIN', 'taggroups      AS taggroups',       'tags.groupId             = taggroups.id')
            ->leftjoin('relations          AS relations',       'tags.id                  = relations.targetId')
            ->leftJoin('entries            AS entries',         'entries.id               = relations.sourceId')
            ->join('JOIN', 'sections       AS sectionsEntries', 'entries.sectionId        = sectionsEntries.id')
            ->join('JOIN', 'entryversions  AS entryversions',   'entryversions.entryId    = entries.id')
            ->join('JOIN', 'elements_sites AS contentEntries',  'entries.id               = contentEntries.elementId')
            ->groupBy('id, elementId')
            ->orderBy('title, entryTitle');

        $records = $query->all();

        $entries = array();
        $tags = [];
        $tagIdPrevious = 0;
        $count = count($records);

        foreach ($records as $index => $record) {
            if ($tagIdPrevious && $record['id'] <> $tagIdPrevious) {
                $tag['entries'] = $entries;
                $tags[] = $tag;
                $entries = array();
            }

            $dateCreatedFormatted = date_format(date_create($record['dateCreated']), "Y-m-d");
            $dateUpdatedFormatted = date_format(date_create($record['dateUpdated']), "Y-m-d");

            $tag = array(
                'id' => $record['id'],
                'title' => $record['title'],
                'groupName' => $record['groupName'],
                'groupHandle' => $record['groupHandle'],
                'dateCreated' => $dateCreatedFormatted,
                'dateUpdated' => $dateUpdatedFormatted,
            );

            $entries[] = array(
                'entryId' => $record['entryId'],
                'entryTitle' => $record['entryTitle'],
                'entrySection' => $record['entrySection']
            );

            if (++$index == $count) {
                $tag = array(
                    'id' => $record['id'],
                    'title' => $record['title'],
                    'groupName' => $record['groupName'],
                    'groupHandle' => $record['groupHandle'],
                    'dateCreated' => $dateCreatedFormatted,
                    'dateUpdated' => $dateUpdatedFormatted,
                    'entries' => $entries,
                );

                $tags[] = $tag;
            }

            $tagIdPrevious = $record['id'];
        }

        return $tags;
    }

    public function getTagCountsForEntry($entryId)
    {
        $query = (new Query())
            ->select('
                tags.id
            ')
            ->from('entries')
            ->leftjoin('relations AS relations', 'entries.id        = relations.sourceId')
            ->leftJoin('tags      AS tags',      'tags.id           = relations.targetId')
            ->where('entries.id = :entryId', array(':entryId' => $entryId))
            ->andWhere('tags.id IS NOT NULL');

        $records = $query->all();
        $tagIds = \array_column($records, 'id');

        $query = (new Query())
            ->select('
                tags.id,
                elements_sites.title,
                count(tags.id) AS count
            ')
            ->from('tags')
            ->join('JOIN', 'relations      AS relations',      'tags.id                  = relations.targetId')
            ->join('JOIN', 'elements_sites AS elements_sites', 'elements_sites.elementId = tags.id')
            ->where(['in', 'tags.id', $tagIds])
            ->groupBy('tags.id, elements_sites.title');

        $records = $query->all();

        return $records;
    }
}
