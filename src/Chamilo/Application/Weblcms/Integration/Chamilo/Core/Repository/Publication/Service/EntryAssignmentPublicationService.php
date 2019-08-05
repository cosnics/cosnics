<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Service;

use Chamilo\Application\Weblcms\Service\CourseService;
use Chamilo\Application\Weblcms\Service\PublicationService;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntryAssignmentPublicationService extends AssignmentPublicationService implements AssignmentPublicationServiceInterface
{
    /**
     * Checks whether or not one of the given content objects are published
     *
     * @param array $contentObjectIds
     *
     * @return bool
     */
    public function areContentObjectsPublished($contentObjectIds = array())
    {
        return ($this->assignmentService->countContentObjectsUsedAsEntryByContentObjectIds($contentObjectIds) > 0);
    }

    /**
     * Deletes the publications by a given content object id
     *
     * @param int $contentObjectId
     */
    public function deleteContentObjectPublicationsByObjectId($contentObjectId)
    {
        // DO NOTHING BECAUSE THE ENTRIES SHOULD NOT BE DELETED
    }

    /**
     * Deletes a specific publication by id
     *
     * @param int $publicationId
     */
    public function deleteContentObjectPublicationsByPublicationId($publicationId)
    {
        // DO NOTHING BECAUSE THE ENTRIES SHOULD NOT BE DELETED
    }

    /**
     * Updates the content object id in the given publication
     *
     * @param int $publicationId
     * @param int $newContentObjectId
     */
    public function updateContentObjectId($publicationId, $newContentObjectId)
    {
        // DO NOTHING BECAUSE THE ENTRIES SHOULD NOT BE UPDATED
    }

    /**
     * Returns the ContentObject publication attributes for a given publication
     *
     * @param int $publicationId
     *
     * @return Attributes
     */
    public function getContentObjectPublicationAttributes($publicationId)
    {
        $entry = $this->assignmentService->findEntryByIdentifier($publicationId);
        return $this->getAttributesForEntry($entry);
    }

    /**
     * Returns the ContentObject publication attributes for a given content object (identified by id)
     *
     * @param int $contentObjectId
     *
     * @return Attributes[]
     */
    public function getContentObjectPublicationAttributesForContentObject($contentObjectId)
    {
        $contentObject = new ContentObject();
        $contentObject->setId($contentObjectId);

        $entries = $this->assignmentService->findEntriesByContentObject($contentObject);
        return $this->getAttributesForEntries($entries);
    }

    /**
     * Returns the ContentObject publication attributes for a given user (identified by id)
     *
     * @param int $userId
     *
     * @return Attributes[]
     */
    public function getContentObjectPublicationAttributesForUser($userId)
    {
        $user = new User();
        $user->setId($userId);

        return $this->getAttributesForEntries($this->assignmentService->findEntriesByUser($user));
    }

    /**
     * Counts the ContentObject publication attributes for a given content object (identified by id)
     *
     * @param int $contentObjectId
     *
     * @return int
     */
    public function countContentObjectPublicationAttributesForContentObject($contentObjectId)
    {
        return $this->assignmentService->countContentObjectsUsedAsEntryByContentObjectIds([$contentObjectId]);
    }

    /**
     * Counts the ContentObject publication attributes for a given user (identified by id)
     *
     * @param int $userId
     *
     * @return int
     */
    public function countContentObjectPublicationAttributesForUser($userId)
    {
        $user = new User();
        $user->setId($userId);

        return $this->assignmentService->countEntriesByUser($user);
    }

    /**
     * @param Entry[] $entries
     *
     * @return Attributes[]
     */
    protected function getAttributesForEntries($entries = [])
    {
        $attributes = [];

        foreach ($entries as $entry)
        {
            $attributes[] = $this->getAttributesForEntry($entry);
        }

        return $attributes;
    }

    /**
     * Builds the publication attributes for the given learning path child
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return Attributes
     */
    protected function getAttributesForEntry(Entry $entry)
    {
        $attributes = new Attributes();
        $attributes->setId($entry->getId());
        $attributes->set_application('Chamilo\Application\Weblcms');
        $attributes->setPublicationContext(\Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry::class);
        $attributes->set_publisher_id($entry->getUserId());
        $attributes->set_date($entry->getSubmitted());
        $attributes->set_title($entry->getContentObject()->get_title());
        $attributes->set_content_object_id($entry->getContentObject()->getId());

        $this->addLocationForEntry($entry, $attributes, Translation::getInstance()->getTranslation('Entry') . ': ');

        return $attributes;
    }
}