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
    public function getTags()
    {
        $query = (new Query())
            ->select('
                tags.id                   AS id,
                contentEntries.elementId  AS elementId,
                taggroups.name            AS groupName,
                taggroups.handle          AS groupHandle,
                content.title             AS title,
                content.dateCreated       AS dateCreated,
                content.dateUpdated       AS dateUpdated,
                entries.id                AS entryId,
                contentEntries.title      AS entryTitle,
                sectionsEntries.handle    AS entrySection
            ')
            ->from('content')
            ->join('JOIN', 'tags          AS tags',            'content.elementId   = tags.id')
            ->join('JOIN', 'taggroups     AS taggroups',       'tags.groupId        = taggroups.id')
            ->leftjoin('relations         AS relations',       'tags.id             = relations.targetId')
            ->leftJoin('entries           AS entries',         'entries.id          = relations.sourceId')
            ->join('JOIN', 'sections      AS sectionsEntries', 'entries.sectionId   = sectionsEntries.id')
            ->join('JOIN', 'entryversions AS entryversions', 'entryversions.entryId = entries.id')
            ->join('JOIN', 'content       AS contentEntries',  'entries.id          = contentEntries.elementId')
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
                content.title,
                count(tags.id) AS count
            ')
            ->from('tags')
            ->join('JOIN', 'relations AS relations', 'tags.id           = relations.targetId')
            ->join('JOIN', 'content   AS content', 'content.elementId = tags.id')
            ->where(['in', 'tags.id', $tagIds])
            ->groupBy('tags.id, content.title');

        $records = $query->all();

        return $records;
    }
}
