<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication\Service;

use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;

interface AssignmentPublicationServiceInterface
{
    /**
     * @return string
     */
    public function getPublicationContext();

    /**
     * Checks whether or not one of the given content objects are published
     *
     * @param array $contentObjectIds
     *
     * @return bool
     */
    public function areContentObjectsPublished($contentObjectIds = array());

    /**
     * Deletes the publications by a given content object id
     *
     * @param int $contentObjectId
     */
    public function deleteContentObjectPublicationsByObjectId($contentObjectId);

    /**
     * Deletes a specific publication by id
     *
     * @param int $publicationId
     */
    public function deleteContentObjectPublicationsByPublicationId($publicationId);

    /**
     * Updates the content object id in the given publication
     *
     * @param int $publicationId
     * @param int $newContentObjectId
     */
    public function updateContentObjectId($publicationId, $newContentObjectId);

    /**
     * Returns the ContentObject publication attributes for a given publication
     *
     * @param int $publicationId
     *
     * @return Attributes
     */
    public function getContentObjectPublicationAttributes($publicationId);

    /**
     * Returns the ContentObject publication attributes for a given content object (identified by id)
     *
     * @param int $contentObjectId
     *
     * @return Attributes[]
     */
    public function getContentObjectPublicationAttributesForContentObject($contentObjectId);

    /**
     * Returns the ContentObject publication attributes for a given user (identified by id)
     *
     * @param int $userId
     *
     * @return Attributes[]
     */
    public function getContentObjectPublicationAttributesForUser($userId);

    /**
     * Counts the ContentObject publication attributes for a given content object (identified by id)
     *
     * @param int $contentObjectId
     *
     * @return int
     */
    public function countContentObjectPublicationAttributesForContentObject($contentObjectId);
    
    /**
     * Counts the ContentObject publication attributes for a given user (identified by id)
     *
     * @param int $userId
     *
     * @return int
     */
    public function countContentObjectPublicationAttributesForUser($userId);
}