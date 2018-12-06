<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Service;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Calculates the navigation options for a given Entry for other entries and other entities
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntryNavigator
{
    /**
     * @var bool
     */
    protected $navigationCalculated = false;

    /**
     * @var Entry
     */
    protected $previousEntry;

    /**
     * @var Entry
     */
    protected $currentEntry;

    /**
     * @var int
     */
    protected $currentEntryPosition;

    /**
     * @var Entry
     */
    protected $nextEntry;

    /**
     * @var DataClass
     */
    protected $previousEntity;

    /**
     * @var DataClass
     */
    protected $currentEntity;

    /**
     * @var int
     */
    protected $currentEntityPosition;

    /**
     * @var DataClass
     */
    protected $nextEntity;

    /**
     * @var DataClass[]
     */
    protected $entities;

    /**
     * Calculates the position of the current entry and the current entity
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $currentEntry
     * @param int $currentEntityType
     * @param int $currentEntityIdentifier
     */
    protected function calculateNavigatorOptions(
        AssignmentDataProvider $assignmentDataProvider, Entry $currentEntry, $currentEntityType,
        $currentEntityIdentifier
    )
    {
        if($this->navigationCalculated)
        {
            return;
        }

        if (!$currentEntry instanceof Entry)
        {
            return;
        }

        $entries = $assignmentDataProvider->findEntriesByEntityTypeAndIdentifiers(
            $currentEntityType, [$currentEntityIdentifier]
        );

        $this->currentEntryPosition = 1;

        foreach ($entries as $entry)
        {
            if ($entry->getId() == $currentEntry->getId())
            {
                $this->currentEntry = $entry;
                continue;
            }

            if (!$this->currentEntry)
            {
                $this->currentEntryPosition ++;
                $this->previousEntry = $entry;
            }
            else
            {
                $this->nextEntry = $entry;
                break;
            }
        }

        $this->entities = $assignmentDataProvider->findEntitiesWithEntriesByEntityType($currentEntityType);
        $this->currentEntityPosition = 1;

        foreach ($this->entities as $entity)
        {
            if ($entity->getId() == $currentEntityIdentifier)
            {
                $this->currentEntity = $entity;
                continue;
            }

            if (!$this->currentEntity)
            {
                $this->currentEntityPosition ++;
                $this->previousEntity = $entity;
            }
            else
            {
                $this->nextEntity = $entity;
                break;
            }
        }

        $this->navigationCalculated = true;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $currentEntry
     * @param int $currentEntityType
     * @param int $currentEntityIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry
     */
    public function getNextEntry(
        AssignmentDataProvider $assignmentDataProvider, Entry $currentEntry, $currentEntityType,
        $currentEntityIdentifier
    )
    {
        $this->calculateNavigatorOptions($assignmentDataProvider, $currentEntry, $currentEntityType, $currentEntityIdentifier);
        return $this->nextEntry;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $currentEntry
     * @param int $currentEntityType
     * @param int $currentEntityIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry
     */
    public function getPreviousEntry(
        AssignmentDataProvider $assignmentDataProvider, Entry $currentEntry, $currentEntityType,
        $currentEntityIdentifier
    )
    {
        $this->calculateNavigatorOptions($assignmentDataProvider, $currentEntry, $currentEntityType, $currentEntityIdentifier);
        return $this->previousEntry;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $currentEntry
     * @param int $currentEntityType
     * @param int $currentEntityIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry
     */
    public function getCurrentEntry(
        AssignmentDataProvider $assignmentDataProvider, Entry $currentEntry, $currentEntityType,
        $currentEntityIdentifier
    )
    {
        $this->calculateNavigatorOptions($assignmentDataProvider, $currentEntry, $currentEntityType, $currentEntityIdentifier);
        return $this->currentEntry;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $currentEntry
     * @param int $currentEntityType
     * @param int $currentEntityIdentifier
     *
     * @return int
     */
    public function getCurrentEntryPosition(
        AssignmentDataProvider $assignmentDataProvider, Entry $currentEntry, $currentEntityType,
        $currentEntityIdentifier
    )
    {
        $this->calculateNavigatorOptions($assignmentDataProvider, $currentEntry, $currentEntityType, $currentEntityIdentifier);
        return $this->currentEntryPosition;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $currentEntry
     * @param int $currentEntityType
     * @param int $currentEntityIdentifier
     *
     * @return DataClass
     */
    public function getNextEntity(
        AssignmentDataProvider $assignmentDataProvider, Entry $currentEntry, $currentEntityType,
        $currentEntityIdentifier
    )
    {
        $this->calculateNavigatorOptions($assignmentDataProvider, $currentEntry, $currentEntityType, $currentEntityIdentifier);
        return $this->nextEntity;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $currentEntry
     * @param int $currentEntityType
     * @param int $currentEntityIdentifier
     *
     * @return DataClass
     */
    public function getPreviousEntity(
        AssignmentDataProvider $assignmentDataProvider, Entry $currentEntry, $currentEntityType,
        $currentEntityIdentifier
    )
    {
        $this->calculateNavigatorOptions($assignmentDataProvider, $currentEntry, $currentEntityType, $currentEntityIdentifier);
        return $this->previousEntity;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $currentEntry
     * @param int $currentEntityType
     * @param int $currentEntityIdentifier
     *
     * @return DataClass
     */
    public function getCurrentEntity(
        AssignmentDataProvider $assignmentDataProvider, Entry $currentEntry, $currentEntityType,
        $currentEntityIdentifier
    )
    {
        $this->calculateNavigatorOptions($assignmentDataProvider, $currentEntry, $currentEntityType, $currentEntityIdentifier);
        return $this->currentEntity;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $currentEntry
     * @param int $currentEntityType
     * @param int $currentEntityIdentifier
     *
     * @return int
     */
    public function getCurrentEntityPosition(
        AssignmentDataProvider $assignmentDataProvider, Entry $currentEntry, $currentEntityType,
        $currentEntityIdentifier
    )
    {
        $this->calculateNavigatorOptions($assignmentDataProvider, $currentEntry, $currentEntityType, $currentEntityIdentifier);
        return $this->currentEntityPosition;
    }

    /**
     * @return DataClass[]
     */
    public function getEntities()
    {
        return $this->entities;
    }

}